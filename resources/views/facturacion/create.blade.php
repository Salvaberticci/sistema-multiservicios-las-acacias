@extends('layouts.main')

@section('title', 'Nueva factura - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="facturaForm()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6" x-ref="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nueva factura</h1>
            <p class="text-gray-500 mt-1">Crear factura paso a paso</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="saveDraft()" class="btn-secondary px-4 py-2.5" :disabled="savingDraft">Guardar borrador</button>
            <a href="/facturacion" class="btn-secondary px-4 py-2.5">Cancelar</a>
        </div>
    </div>
    
    <!-- Stepper -->
    <div class="mb-8" x-ref="stepper">
        <div class="flex items-center" role="tablist">
            <template x-for="(step, index) in steps" :key="step.key">
                <div class="flex items-center">
                    <button :class="['flex items-center gap-2 px-4 py-3 rounded-lg transition', currentStep >= index ? 'bg-brand-blue text-white' : currentStep > index ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500']" @click="goToStep(index)" :disabled="currentStep < index && index > 0" type="button">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium" :class="currentStep > index ? 'bg-green-600' : currentStep >= index ? 'bg-brand-blue-dark' : 'bg-gray-300'" x-text="currentStep > index ? '✓' : index + 1"></span>
                        <span class="hidden sm:block font-medium" x-text="step.label"></span>
                    </button>
                    <div x-show="index < steps.length - 1" class="h-1 flex-1 mx-2 transition-all duration-300" :class="currentStep > index ? 'bg-green-500' : currentStep >= index ? 'bg-brand-blue' : 'bg-gray-200'"></div>
                </div>
            </template>
        </div>
    </div>
    
    <!-- Form Steps -->
    <form @submit.prevent="submit" class="space-y-6">
        <!-- Step 1: Cliente -->
        <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">1. Seleccionar cliente</h2>
                
                <div class="relative">
                    <select x-model="form.clienteId" @change="onClienteChange()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent appearance-none" required>
                        <option value="">Buscar o seleccionar cliente...</option>
                        <template x-for="cliente in clientes" :key="cliente.id">
                            <option :value="cliente.id" x-text="cliente.nombre + ' - ' + cliente.telefono"></option>
                        </template>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                
                <p x-show="errors.clienteId" class="mt-2 text-sm text-red-600" x-text="errors.clienteId"></p>
                
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button type="button" @click="showNuevoCliente = true" class="text-sm text-brand-blue hover:text-brand-blue-dark font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo cliente
                    </button>
                </div>
                
                <!-- Cliente seleccionado preview -->
                <div x-show="selectedCliente" class="mt-4 p-4 bg-green-50 rounded-lg border border-green-100 animate-fade-in">
                    <p class="font-medium text-green-800" x-text="selectedCliente?.nombre || 'Sin cliente seleccionado'"></p>
                    <p class="text-sm text-green-700" x-text="selectedCliente?.telefono || ''"></p>
                    <p class="text-sm text-green-700" x-text="selectedCliente?.email || ''"></p>
                </div>
            </div>
        </div>
        
        <!-- Step 2: Líneas -->
        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">2. Servicios y productos</h2>
                    <button type="button" @click="addLinea()" class="btn-secondary text-sm px-3 py-1.5">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Añadir línea
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-2 px-2">Servicio / Producto</th>
                                <th class="pb-2 px-2 text-center">Cant.</th>
                                <th class="pb-2 px-2 text-right">Precio unit.</th>
                                <th class="pb-2 px-2 text-right">Subtotal</th>
                                <th class="pb-2 px-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(linea, index) in form.lineas" :key="linea.id">
                                <tr class="border-b border-gray-50" x-ref="lineaRow">
                                    <td class="py-3 px-2">
                                        <select x-model="linea.servicioId" @change="calculateLinea(index)" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" required>
                                            <option value="">Seleccionar...</option>
                                            <template x-for="s in servicios" :key="s.id">
                                                <option :value="s.id" x-text="s.nombre + ' - ' + utils.formatCurrency(s.precio)"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <input type="number" x-model.number="linea.cantidad" @input="calculateLinea(index)" min="1" class="w-20 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-center text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" required>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <input type="number" x-model.number="linea.precioUnitario" @input="calculateLinea(index)" step="0.01" min="0" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-right text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" required>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="font-medium text-gray-900" x-text="utils.formatCurrency(linea.subtotal)"></span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" @click="removeLinea(index)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="form.lineas.length === 0">
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <p>No hay líneas. Haga clic en "Añadir línea" para empezar.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Step 3: Totales -->
        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">3. Resumen y totales</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900" x-text="utils.formatCurrency(totales.subtotal)"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">IVA (21%)</span>
                            <span class="font-medium text-gray-900" x-text="utils.formatCurrency(totales.iva)"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Descuento</span>
                            <span class="font-medium text-gray-900" x-text="utils.formatCurrency(totales.descuento)"></span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 bg-brand-blue-soft rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-brand-blue-dark">Total a pagar</p>
                                <p class="text-3xl font-bold text-brand-blue" x-text="utils.formatCurrency(totales.total)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-brand-blue-dark">Líneas</p>
                                <p class="text-2xl font-bold text-brand-blue" x-text="form.lineas.length"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Step 4: Pago -->
        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">4. Pago</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de pago</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <template x-for="metodo in metodosPago" :key="metodo">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metodoPago" :value="metodo" x-model="form.metodoPago" class="sr-only">
                                    <div class="p-4 border-2 rounded-xl text-center transition-all" :class="form.metodoPago === metodo ? 'border-brand-blue bg-brand-blue-soft' : 'border-gray-200 hover:border-gray-300'">
                                        <svg :class="['w-8 h-8 mx-auto mb-2', form.metodoPago === metodo ? 'text-brand-blue' : 'text-gray-400']" x-html="metodoIcon(metodo)"></svg>
                                        <p class="text-sm font-medium" :class="form.metodoPago === metodo ? 'text-brand-blue' : 'text-gray-600'" x-text="metodoLabel(metodo)"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                    
                    <div x-show="form.metodoPago === 'mixto'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Detalle de pago mixto</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Efectivo</label>
                                <input type="number" x-model.number="form.efectivo" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Tarjeta</label>
                                <input type="number" x-model.number="form.tarjeta" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Transferencia</label>
                                <input type="number" x-model.number="form.transferencia" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Total pagado</label>
                                <input type="number" x-model.number="form.totalPagado" step="0.01" min="0" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-900 font-semibold">
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="form.metodoPago && form.metodoPago !== 'mixto'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monto recibido</label>
                        <input type="number" x-model.number="form.montoRecibido" @input="calculateCambio()" step="0.01" min="0" class="w-full md:w-1/3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="0.00">
                        <p class="mt-2 text-sm text-gray-600">Cambio: <span class="font-medium text-green-600" x-text="utils.formatCurrency(cambio)"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Navigation Buttons -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-100" x-ref="navButtons">
        <button type="button" @click="prevStep()" x-show="currentStep > 0" class="btn-secondary px-6 py-2.5">Anterior</button>
        <div class="flex gap-3">
            <button type="button" @click="saveDraft()" x-show="currentStep < 3" class="btn-secondary px-6 py-2.5" :disabled="savingDraft">Guardar borrador</button>
            <button type="button" @click="nextStep()" x-show="currentStep < 3" class="btn-primary px-6 py-2.5" :disabled="!canProceed()">Siguiente</button>
            <button type="submit" x-show="currentStep === 3" class="btn-primary px-6 py-2.5 flex items-center gap-2" :disabled="submitting || !canSubmit()">
                <span x-show="!submitting">Emitir factura</span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Emitiendo...
                </span>
            </button>
        </div>
    </div>
    
    <!-- Nuevo Cliente Modal -->
    <div x-show="showNuevoCliente" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showNuevoCliente = false" style="display: none;">
        <div class="fixed inset-0 bg-black/50" @click="showNuevoCliente = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto" x-ref="clienteModal">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Nuevo cliente</h3>
                <button @click="showNuevoCliente = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <form @submit.prevent="createCliente" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" x-model="nuevoCliente.nombre" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                    <input type="tel" x-model="nuevoCliente.telefono" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" x-model="nuevoCliente.email" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <textarea x-model="nuevoCliente.direccion" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue"></textarea>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" @click="showNuevoCliente = false" class="flex-1 btn-secondary">Cancelar</button>
                    <button type="submit" class="flex-1 btn-primary" :disabled="creatingCliente">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function facturaForm() {
        return {
            currentStep: 0,
            steps: [
                { key: 'cliente', label: 'Cliente' },
                { key: 'lineas', label: 'Líneas' },
                { key: 'totales', label: 'Totales' },
                { key: 'pago', label: 'Pago' }
            ],
            form: {
                clienteId: '',
                lineas: [],
                metodoPago: '',
                montoRecibido: 0,
                efectivo: 0,
                tarjeta: 0,
                transferencia: 0,
                totalPagado: 0
            },
            errors: {},
            savingDraft: false,
            submitting: false,
            creatingCliente: false,
            showNuevoCliente: false,
            nuevoCliente: { nombre: '', telefono: '', email: '', direccion: '' },
            clientes: [],
            servicios: [],
            metodosPago: ['efectivo', 'tarjeta', 'transferencia', 'mixto'],
            
            init() {
                this.clientes = this.$store.clientes.getAll();
                this.servicios = [
                    { id: 1, nombre: 'Lavado exterior', precio: 15 },
                    { id: 2, nombre: 'Lavado interior', precio: 20 },
                    { id: 3, nombre: 'Lavado completo', precio: 30 },
                    { id: 4, nombre: 'Pulido faros', precio: 25 },
                    { id: 5, nombre: 'Aspirado tapicería', precio: 10 },
                    { id: 6, nombre: 'Limpieza motor', precio: 35 },
                    { id: 7, nombre: 'Tratamiento ozono', precio: 40 },
                    { id: 8, nombre: 'Encerado', precio: 25 }
                ];
                
                // Add first empty line
                this.addLinea();
                
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from(this.$refs.header, { opacity: 0, y: -10, duration: 0.3, ease: 'power2.out' });
                gsap.from(this.$refs.stepper, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.1 });
                gsap.from(this.$refs.navButtons, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.2 });
            },
            
            get selectedCliente() {
                if (!this.form.clienteId) return null;
                return this.clientes.find(c => c.id == this.form.clienteId) || null;
            },
            
            onClienteChange() {
                this.errors.clienteId = null;
            },
            
            get totales() {
                const subtotal = this.form.lineas.reduce((sum, l) => sum + l.subtotal, 0);
                const iva = subtotal * 0.21;
                const descuento = 0;
                const total = subtotal + iva - descuento;
                return { subtotal, iva, descuento, total };
            },
            
            get cambio() {
                if (!this.form.montoRecibido) return 0;
                return Math.max(0, this.form.montoRecibido - this.totales.total);
            },
            
            addLinea() {
                this.form.lineas.push({
                    id: Date.now() + Math.random(),
                    servicioId: '',
                    cantidad: 1,
                    precioUnitario: 0,
                    subtotal: 0
                });
            },
            
            removeLinea(index) {
                this.form.lineas.splice(index, 1);
            },
            
            calculateLinea(index) {
                const linea = this.form.lineas[index];
                const servicio = this.servicios.find(s => s.id == linea.servicioId);
                if (servicio && !linea.precioUnitario) {
                    linea.precioUnitario = servicio.precio;
                }
                linea.subtotal = (linea.cantidad || 0) * (linea.precioUnitario || 0);
            },
            
            canProceed() {
                if (this.currentStep === 0) return !!this.form.clienteId;
                if (this.currentStep === 1) return this.form.lineas.length > 0 && this.form.lineas.every(l => l.servicioId && l.cantidad > 0);
                if (this.currentStep === 2) return true;
                return false;
            },
            
            canSubmit() {
                return this.form.metoPago && this.form.montoRecibido >= this.totales.total;
            },
            
            nextStep() {
                if (!this.canProceed()) return;
                this.currentStep++;
                this.$nextTick(() => this.animateStep());
            },
            
            prevStep() {
                this.currentStep--;
                this.$nextTick(() => this.animateStep());
            },
            
            goToStep(index) {
                if (index <= this.currentStep || (index === this.currentStep + 1 && this.canProceed())) {
                    this.currentStep = index;
                    this.$nextTick(() => this.animateStep());
                }
            },
            
            animateStep() {
                if (window.prefersReducedMotion?.()) return;
                // Step content animates via x-transition
            },
            
            async saveDraft() {
                this.savingDraft = true;
                this.$store.facturas.saveDraft(this.form);
                this.$store.toast.success('Borrador guardado');
                await new Promise(r => setTimeout(r, 500));
                this.savingDraft = false;
            },
            
            async submit() {
                if (!this.canSubmit()) return;
                
                this.submitting = true;
                
                const factura = {
                    clienteId: this.form.clienteId,
                    clienteNombre: this.selectedCliente?.nombre,
                    clienteTelefono: this.selectedCliente?.telefono,
                    lineas: this.form.lineas.map(l => ({
                        servicioId: l.servicioId,
                        servicio: this.servicios.find(s => s.id == l.servicioId)?.nombre,
                        cantidad: l.cantidad,
                        precioUnitario: l.precioUnitario,
                        subtotal: l.subtotal
                    })),
                    subtotal: this.totales.subtotal,
                    iva: this.totales.iva,
                    total: this.totales.total,
                    estado: 'pagada',
                    metodoPago: this.form.metodoPago,
                    montoRecibido: this.form.montoRecibido,
                    cambio: this.cambio
                };
                
                try {
                    const created = this.$store.facturas.create(factura);
                    this.$store.toast.success(`Factura ${created.numero} emitida correctamente`);
                    window.location.href = `/facturacion/${created.id}`;
                } catch (e) {
                    this.$store.toast.error('Error al emitir factura');
                } finally {
                    this.submitting = false;
                }
            },
            
            async createCliente() {
                this.creatingCliente = true;
                try {
                    const cliente = this.$store.clientes.create(this.nuevoCliente);
                    this.clientes = this.$store.clientes.getAll();
                    this.form.clienteId = cliente.id;
                    this.showNuevoCliente = false;
                    this.nuevoCliente = { nombre: '', telefono: '', email: '', direccion: '' };
                    this.$store.toast.success('Cliente creado');
                } finally {
                    this.creatingCliente = false;
                }
            },
            
            metodoIcon(metodo) {
                const icons = {
                    efectivo: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    tarjeta: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
                    transferencia: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>',
                    mixto: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>'
                };
                return icons[metodo] || '';
            },
            
            metodoLabel(metodo) {
                const labels = {
                    efectivo: 'Efectivo',
                    tarjeta: 'Tarjeta',
                    transferencia: 'Transferencia',
                    mixto: 'Mixto'
                };
                return labels[metodo] || metodo;
            },
            
            calculateCambio() {
                // Reactive via getter
            }
        }
    }
</script>
@endsection