@extends('layouts.main')

@section('title', 'Detalle vehículo - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="vehiculosShow()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="/vehiculos" class="inline-flex items-center gap-2 text-sm text-brand-blue hover:text-brand-blue-dark font-medium mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Vehículo <span class="font-mono" x-text="vehiculo?.placa"></span></h1>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/vehiculos/salida/${vehiculo?.placa}`" x-show="vehiculo?.estado === 'dentro'" class="btn-primary px-4 py-2">Registrar salida</a>
            <a href="/vehiculos" class="btn-secondary px-4 py-2">Volver</a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden" x-ref="card">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-brand-blue-soft flex items-center justify-center text-brand-blue">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 7h.01M15 15h.01M15 15h.01M3 20c6.657 0 11.25-5.603 11.25-12.25S9.657 5.25 16 5.25c6.657 0 11.25 5.603 11.25 12.25M18 13h-6a4 4 0 00-4 4v-6a4 4 0 014-4z"></path></svg>
                </div>
                <div>
                    <p class="font-mono text-xl font-medium text-gray-900" x-text="vehiculo?.placa"></p>
                    <p class="text-gray-500" x-text="vehiculo?.clienteNombre"></p>
                </div>
            </div>
            <div class="mt-4">
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full" :class="vehiculo?.estado === 'dentro' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'" x-text="vehiculo?.estado"></span>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="p-6 border-b border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="vehiculo?.tamano"></p>
                <p class="text-sm text-gray-500">Tamaño</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="utils.formatDate(vehiculo?.entrada?.fechaEntrada)"></p>
                <p class="text-sm text-gray-500">Fecha de entrada</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="vehiculo?.historial?.length || 0"></p>
                <p class="text-sm text-gray-500">Salidas registradas</p>
            </div>
        </div>
        
        <!-- Historial -->
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Historial</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">Fecha entrada</th>
                            <th class="px-4 py-3">Fecha salida</th>
                            <th class="px-4 py-3">Tarifa</th>
                            <th class="px-4 py-3">Método pago</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="h in vehiculo?.historial || []" :key="h.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(h.fechaEntrada)"></td>
                                <td class="px-4 py-4 text-sm text-gray-600" x-text="h.fechaSalida ? utils.formatDate(h.fechaSalida) : 'Dentro'"></td>
                                <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="h.tarifa ? utils.formatCurrency(h.tarifa) : '-'"></td>
                                <td class="px-4 py-4 text-sm text-gray-500" x-text="h.metodoPago || '-'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function vehiculosShow() {
        return {
            vehiculo: null,
            
            init() {
                const placa = window.location.pathname.split('/').pop();
                this.vehiculo = this.$store.vehiculos.getByPlaca(placa);
                
                if (!this.vehiculo) {
                    this.$store.toast.error('Vehículo no encontrado');
                    window.location.href = '/vehiculos';
                    return;
                }
                
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.card, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out' });
            }
        }
    }
</script>
@endsection