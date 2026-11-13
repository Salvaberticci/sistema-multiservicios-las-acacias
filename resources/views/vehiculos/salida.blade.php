@extends('layouts.main')

@section('title', 'Registrar salida - Multiservicios Las Acacias')

@section('content')
<div class="max-w-2xl mx-auto" x-data="vehiculoSalida('{{ $placa }}')">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrar salida</h1>
            <p class="text-gray-500 mt-1" x-text="'Placa: ' + vehiculo?.placa"></p>
        </div>
        <a href="/vehiculos" class="btn-secondary px-4 py-2">Volver</a>
    </div>
    
    <div class="space-y-6">
        <!-- Vehicle Info Card -->
        <div class="bg-white rounded-xl border border-gray-100 p-6" x-ref="infoCard">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</p>
                    <p class="font-mono text-xl font-bold text-gray-900" x-text="vehiculo?.placa"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</p>
                    <p class="font-medium text-gray-900" x-text="vehiculo?.clienteNombre"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño</p>
                    <p class="font-medium text-gray-900" x-text="vehiculo?.tamano"></p>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Entrada</p>
                    <p class="font-medium text-gray-900" x-text="utils.formatDate(vehiculo?.entrada?.fechaEntrada)"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tiempo dentro</p>
                    <p class="text-2xl font-bold text-brand-blue" x-text="tiempoDentro"></p>
                </div>
            </div>
        </div>
        
        <!-- Tarifa calculada -->
        <div class="bg-brand-blue-soft rounded-xl border border-brand-blue-soft p-6" x-ref="tarifaCard">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-brand-blue-dark">Tarifa estimada</p>
                    <p class="text-3xl font-bold text-brand-blue" x-text="utils.formatCurrency(tarifaEstimada)"></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-brand-blue-dark">Tarifa base</p>
                    <p class="font-medium text-brand-blue" x-text="utils.formatCurrency(tarifaBase)"></p>
                    <p class="text-xs text-brand-blue-dark" x-text="recargoInfo"></p>
                </div>
            </div>
        </div>
        
        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de pago *</label>
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
                
                <div x-show="form.metodoPago === 'mixto'" class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700">Detalle de pago mixto</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Efectivo</label>
                            <input type="number" x-model.number="form.efectivo" @input="calcularTotalPagado()" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Tarjeta</label>
                            <input type="number" x-model.number="form.tarjeta" @input="calcularTotalPagado()" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Transferencia</label>
                            <input type="number" x-model.number="form.transferencia" @input="calcularTotalPagado()" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Total pagado</label>
                            <input type="number" x-model.number="form.totalPagado" step="0.01" min="0" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-900 font-semibold">
                        </div>
                    </div>
                </div>
                
                <div x-show="form.metodoPago && form.metodoPago !== 'mixto'" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Monto recibido</label>
                    <input type="number" x-model.number="form.montoRecibido" @input="calcularCambio()" step="0.01" min="0" class="w-full md:w-1/3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="0.00">
                    <p class="text-sm text-gray-600">Cambio: <span class="font-medium text-green-600" x-text="utils.formatCurrency(cambio)"></span></p>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea x-model="form.observaciones" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Opcional"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="/vehiculos" class="btn-secondary px-6 py-2.5">Cancelar</a>
            <button type="submit" class="btn-primary px-6 py-2.5" :disabled="submitting || !canSubmit()">
                <span x-show="!submitting">Registrar salida</span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Registrando...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function vehiculoSalida(placa) {
        return {
            vehiculo: null,
            tiempoDentro: 'Calculando...',
            tarifaEstimada: 0,
            tarifaBase: 0,
            recargoInfo: '',
            form: { metodoPago: '', montoRecibido: 0, efectivo: 0, tarjeta: 0, transferencia: 0, totalPagado: 0, observaciones: '' },
            metodosPago: ['efectivo', 'tarjeta', 'transferencia', 'mixto'],
            submitting: false,
            intervalId: null,
            
            init() {
                this.loadVehiculo(placa);
                this.startTimer();
            },
            
            loadVehiculo(placa) {
                this.vehiculo = this.$store.vehiculos.getByPlaca(placa);
                if (!this.vehiculo || !this.vehiculo.entrada) {
                    this.$store.toast.error('Vehículo no encontrado o no está dentro');
                    window.location.href = '/vehiculos';
                    return;
                }
                this.updateTarifa();
            },
            
            startTimer() {
                this.updateTiempoDentro();
                this.intervalId = setInterval(() => this.updateTiempoDentro(), 30000);
            },
            
            updateTiempoDentro() {
                if (!this.vehiculo?.entrada) return;
                const entrada = new Date(this.vehiculo.entrada.fechaEntrada);
                const ahora = new Date();
                const diffMs = ahora - entrada;
                const horas = Math.floor(diffMs / 3600000);
                const mins = Math.floor((diffMs % 3600000) / 60000);
                this.tiempoDentro = `${horas}h ${mins}m`;
                this.updateTarifa();
            },
            
            updateTarifa() {
                const tarifas = this.$store.settings.getTarifas();
                this.tarifaBase = tarifas[this.vehiculo.tamano] || 20;
                const recargo = tarifas.recargoHora || 5;
                
                const entrada = new Date(this.vehiculo.entrada.fechaEntrada);
                const ahora = new Date();
                const horas = Math.ceil((ahora - entrada) / 3600000);
                
                this.tarifaEstimada = this.tarifaBase + Math.max(0, horas - 1) * recargo;
                this.recargoInfo = horas > 1 ? `+ ${horas - 1}h extra × ${utils.formatCurrency(recargo)}` : 'Sin recargo';
            },
            
            get cambio() {
                if (!this.form.montoRecibido) return 0;
                return Math.max(0, this.form.montoRecibido - this.tarifaEstimada);
            },
            
            calcularTotalPagado() {
                this.form.totalPagado = (this.form.efectivo || 0) + (this.form.tarjeta || 0) + (this.form.transferencia || 0);
            },
            
            calcularCambio() {
                // Reactive via getter
            },
            
            canSubmit() {
                if (this.form.metodoPago === 'mixto') {
                    return this.form.totalPagado >= this.tarifaEstimada;
                }
                return this.form.montoRecibido >= this.tarifaEstimada;
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
                const labels = { efectivo: 'Efectivo', tarjeta: 'Tarjeta', transferencia: 'Transferencia', mixto: 'Mixto' };
                return labels[metodo] || metodo;
            },
            
            async submit() {
                if (!this.canSubmit()) return;
                
                this.submitting = true;
                const result = this.$store.vehiculos.registrarSalida(this.vehiculo.placa, this.form);
                if (result) {
                    this.$store.toast.success(`Salida registrada. Tarifa: ${this.$store.utils.formatCurrency(result.tarifa)}`);
                    if (this.intervalId) clearInterval(this.intervalId);
                    window.location.href = '/vehiculos';
                } else {
                    this.$store.toast.error('Error al registrar salida');
                }
                this.submitting = false;
            }
        }
    }
</script>
@endsection