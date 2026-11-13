@extends('layouts.main')

@section('title', 'Vehículos - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="vehiculosIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vehículos</h1>
            <p class="text-gray-500 mt-1">Entradas, salidas y historial</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/vehiculos/entrada" class="btn-primary px-4 py-2.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 19l7-7 3 3-7 7-3-3z"></path></svg>
                Registrar entrada
            </a>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4" x-ref="statsGrid">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-brand-blue" x-text="stats.dentro"></p>
            <p class="text-sm text-gray-500">Dentro</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-green-600" x-text="stats.fuera"></p>
            <p class="text-sm text-gray-500">Fuera</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900" x-text="stats.total"></p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100">
            <nav class="flex gap-1 px-6" role="tablist">
                <button @click="activeTab = 'dentro'" :class="tabClass('dentro')" role="tab">Dentro (<span x-text="stats.dentro"></span>)</button>
                <button @click="activeTab = 'fuera'" :class="tabClass('fuera')" role="tab">Fuera (<span x-text="stats.fuera"></span>)</button>
                <button @click="activeTab = 'historial'" :class="tabClass('historial')" role="tab">Historial</button>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="p-0">
            <div x-show="activeTab === 'dentro' || activeTab === 'fuera'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" x-model="search" @input="filter()" placeholder="Buscar placa, cliente..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
                    </div>
                    <select x-model="tamanoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option value="">Todos los tamaños</option>
                        <option value="pequeño">Pequeño</option>
                        <option value="mediano">Mediano</option>
                        <option value="grande">Grande</option>
                    </select>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-4 py-3">Placa</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Tamaño</th>
                                <th class="px-4 py-3">Entrada</th>
                                <th class="px-4 py-3">Tiempo dentro</th>
                                <th class="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="v in filteredVehiculos" :key="v.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-sm font-medium text-gray-900" x-text="v.placa"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-medium text-gray-900" x-text="v.clienteNombre"></p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="v.tamano === 'pequeño' ? 'bg-blue-100 text-blue-800' : v.tamano === 'mediano' ? 'bg-cyan-100 text-cyan-800' : 'bg-indigo-100 text-indigo-800'" x-text="v.tamano"></span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(v.entrada.fechaEntrada)"></td>
                                    <td class="px-4 py-4 text-sm font-medium text-brand-blue" x-text="v.tiempoDentro"></td>
                                    <td class="px-4 py-4 text-right">
                                        <a :href="`/vehiculos/salida/${v.placa}`" class="btn-primary text-sm px-4 py-2">Registrar salida</a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredVehiculos.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                    <p>No hay vehículos en esta categoría</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div x-show="activeTab === 'historial'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4">
                    <div class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" x-model="histSearch" @input="filterHistorial()" placeholder="Buscar placa, cliente..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-4 py-3">Placa</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Entrada</th>
                                <th class="px-4 py-3">Salida</th>
                                <th class="px-4 py-3 text-right">Tarifa</th>
                                <th class="px-4 py-3">Método pago</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="v in vehiculos" :key="v.id">
                                <template x-for="h in v.historial" :key="h.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <span class="font-mono text-sm font-medium text-gray-900" x-text="v.placa"></span>
                                        </td>
                                        <td class="px-4 py-4" x-text="v.clienteNombre"></td>
                                        <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(h.fechaEntrada)"></td>
                                        <td class="px-4 py-4 text-sm text-gray-600" x-text="h.fechaSalida ? utils.formatDate(h.fechaSalida) : 'Dentro'"></td>
                                        <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="h.tarifa ? utils.formatCurrency(h.tarifa) : '-'"></td>
                                        <td class="px-4 py-4 text-sm text-gray-500" x-text="h.metodoPago || '-'"></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function vehiculosIndex() {
        return {
            activeTab: 'dentro',
            search: '',
            tamanoFilter: '',
            histSearch: '',
            vehiculos: [],
            filteredVehiculos: [],
            stats: { dentro: 0, fuera: 0, total: 0 },
            
            init() {
                this.vehiculos = this.$store.vehiculos.getAll();
                this.filter();
                this.updateStats();
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.statsGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
            },
            
            updateStats() {
                this.stats = this.$store.vehiculos.getStats();
            },
            
            tabClass(tab) {
                return `px-4 py-2 text-sm font-medium rounded-t-lg transition ${this.activeTab === tab ? 'text-brand-blue border-b-2 border-brand-blue' : 'text-gray-500 hover:text-gray-700'}`;
            },
            
            filter() {
                let result = [...this.vehiculos];
                
                if (this.activeTab === 'dentro') {
                    result = result.filter(v => v.estado === 'dentro');
                } else if (this.activeTab === 'fuera') {
                    result = result.filter(v => v.estado === 'fuera');
                }
                
                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(v => 
                        v.placa.toLowerCase().includes(q) ||
                        v.clienteNombre.toLowerCase().includes(q)
                    );
                }
                
                if (this.tamanoFilter) {
                    result = result.filter(v => v.tamano === this.tamanoFilter);
                }
                
                // Add tiempoDentro for display
                this.filteredVehiculos = result.map(v => {
                    if (v.estado === 'dentro' && v.entrada) {
                        const entrada = new Date(v.entrada.fechaEntrada);
                        const ahora = new Date();
                        const diffMs = ahora - entrada;
                        const horas = Math.floor(diffMs / 3600000);
                        const mins = Math.floor((diffMs % 3600000) / 60000);
                        return { ...v, tiempoDentro: `${horas}h ${mins}m` };
                    }
                    return { ...v, tiempoDentro: '-' };
                });
            },
            
            filterHistorial() {
                // Handled by template filtering
            }
        }
    }
</script>
@endsection