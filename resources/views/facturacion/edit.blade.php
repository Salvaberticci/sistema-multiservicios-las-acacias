@extends('layouts.main')

@section('title', 'Editar factura - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="facturaEdit('{{ $id }}')">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar factura</h1>
            <p class="text-gray-500 mt-1" x-text="factura?.numero"></p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/facturacion/${factura.id}`" class="btn-secondary px-4 py-2">Ver factura</a>
            <a href="/facturacion" class="btn-secondary px-4 py-2">Cancelar</a>
        </div>
    </div>
    
    <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select x-model="form.clienteId" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" required>
                        <template x-for="cliente in clientes" :key="cliente.id">
                            <option :value="cliente.id" x-text="cliente.nombre"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select x-model="form.estado" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option value="borrador">Borrador</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                        <option value="anulada">Anulada</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Líneas</label>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-2 px-2">Servicio</th>
                                <th class="pb-2 px-2 text-center">Cant.</th>
                                <th class="pb-2 px-2 text-right">Precio unit.</th>
                                <th class="pb-2 px-2 text-right">Subtotal</th>
                                <th class="pb-2 px-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(linea, index) in form.lineas" :key="linea.id">
                                <tr class="border-b border-gray-50">
                                    <td class="py-3 px-2">
                                        <select x-model="linea.servicioId" @change="calculateLinea(index)" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                            <option value="">Seleccionar...</option>
                                            <template x-for="s in servicios" :key="s.id">
                                                <option :value="s.id" x-text="s.nombre + ' - ' + utils.formatCurrency(s.precio)"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <input type="number" x-model.number="linea.cantidad" @input="calculateLinea(index)" min="1" class="w-20 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-center text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <input type="number" x-model.number="linea.precioUnitario" @input="calculateLinea(index)" step="0.01" min="0" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-right text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="font-medium text-gray-900" x-text="utils.formatCurrency(linea.subtotal)"></span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <button type="button" @click="removeLinea(index)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <button type="button" @click="addLinea()" class="mt-3 btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Añadir línea
                </button>
            </div>
            
            <div class="mt-6 text-right">
                <p class="text-lg font-semibold text-gray-900">Total: <span class="text-brand-blue" x-text="utils.formatCurrency(total)"></span></p>
            </div>
        </div>
        
        <div class="flex justify-end gap-3">
            <a :href="`/facturacion/${factura.id}`" class="btn-secondary px-6 py-2.5">Cancelar</a>
            <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function facturaEdit(id) {
        return {
            factura: {},
            form: { clienteId: '', estado: 'borrador', lineas: [] },
            clientes: [],
            servicios: [
                { id: 1, nombre: 'Lavado exterior', precio: 15 },
                { id: 2, nombre: 'Lavado interior', precio: 20 },
                { id: 3, nombre: 'Lavado completo', precio: 30 },
                { id: 4, nombre: 'Pulido faros', precio: 25 },
                { id: 5, nombre: 'Aspirado tapicería', precio: 10 }
            ],
            saving: false,
            
            init() {
                this.loadFactura(id);
                this.clientes = this.$store.clientes.getAll();
            },
            
            loadFactura(id) {
                this.factura = this.$store.facturas.getById(id) || {};
                if (!this.factura.id) {
                    this.$store.toast.error('Factura no encontrada');
                    window.location.href = '/facturacion';
                    return;
                }
                this.form.clienteId = this.factura.clienteId;
                this.form.estado = this.factura.estado;
                this.form.lineas = this.factura.lineas.map(l => ({ ...l, id: l.id || Date.now() + Math.random() }));
            },
            
            get total() {
                return this.form.lineas.reduce((sum, l) => sum + l.subtotal, 0) * 1.21;
            },
            
            addLinea() {
                this.form.lineas.push({ id: Date.now() + Math.random(), servicioId: '', cantidad: 1, precioUnitario: 0, subtotal: 0 });
            },
            
            removeLinea(index) {
                this.form.lineas.splice(index, 1);
            },
            
            calculateLinea(index) {
                const linea = this.form.lineas[index];
                const servicio = this.servicios.find(s => s.id == linea.servicioId);
                if (servicio && !linea.precioUnitario) linea.precioUnitario = servicio.precio;
                linea.subtotal = (linea.cantidad || 0) * (linea.precioUnitario || 0);
            },
            
            async submit() {
                this.saving = true;
                const data = {
                    clienteId: this.form.clienteId,
                    estado: this.form.estado,
                    lineas: this.form.lineas,
                    subtotal: this.form.lineas.reduce((sum, l) => sum + l.subtotal, 0),
                    iva: this.form.lineas.reduce((sum, l) => sum + l.subtotal, 0) * 0.21,
                    total: this.form.lineas.reduce((sum, l) => sum + l.subtotal, 0) * 1.21
                };
                this.$store.facturas.update(this.factura.id, data);
                this.$store.toast.success('Factura actualizada');
                window.location.href = `/facturacion/${this.factura.id}`;
            }
        }
    }
</script>
@endsection