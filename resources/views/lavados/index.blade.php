@extends('layouts.main')

@section('title', 'Lavados - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="lavadosIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Lavados</h1>
            <p class="text-gray-500 mt-1">Registro de lavados y comisiones</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/lavados/planificacion" class="btn-secondary px-4 py-2.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Asignación
            </a>
            <a href="/lavados/create" class="btn-primary px-4 py-2.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Registrar lavado
            </a>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-ref="statsGrid">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-brand-blue" x-text="stats.totalHoy"></p>
            <p class="text-sm text-gray-500">Lavados hoy</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-green-600" x-text="utils.formatCurrency(stats.ingresoHoy)"></p>
            <p class="text-sm text-gray-500">Ingreso hoy</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-amber-600" x-text="utils.formatCurrency(stats.comisionHoy)"></p>
            <p class="text-sm text-gray-500">Comisión hoy</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900" x-text="stats.totalMes"></p>
            <p class="text-sm text-gray-500">Total mes</p>
        </div>
    </div>
    
    <!-- Toolbar -->
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" x-model="search" @input="filter()" placeholder="Buscar placa, cliente, empleado..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
            </div>
            
            <select x-model="empleadoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                <option value="">Todos los empleados</option>
                <template x-for="emp in empleados" :key="emp.id">
                    <option :value="emp.id" x-text="emp.nombre"></option>
                </template>
            </select>
            
            <select x-model="tamanoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                <option value="">Todos los tamaños</option>
                <option value="pequeño">Pequeño</option>
                <option value="mediano">Mediano</option>
                <option value="grande">Grande</option>
            </select>
            
            <select x-model="estadoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                <option value="">Todos los estados</option>
                <option value="completado">Completado</option>
                <option value="anulado">Anulado</option>
            </select>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Vehículo</th>
                        <th class="px-4 py-3">Tamaño</th>
                        <th class="px-4 py-3">Empleado</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-right">Comisión</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="lavado in filteredLavados" :key="lavado.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(lavado.fecha)"></td>
                            <td class="px-4 py-4">
                                <p class="font-medium text-gray-900" x-text="lavado.vehiculoPlaca"></p>
                                <p class="text-xs text-gray-500" x-text="lavado.clienteNombre"></p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="lavado.vehiculoTamano === 'pequeño' ? 'bg-blue-100 text-blue-800' : lavado.vehiculoTamano === 'mediano' ? 'bg-cyan-100 text-cyan-800' : 'bg-indigo-100 text-indigo-800'" x-text="lavado.vehiculoTamano"></span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900" x-text="lavado.empleadoNombre"></td>
                            <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="utils.formatCurrency(lavado.precio)"></td>
                            <td class="px-4 py-4 text-right font-medium text-amber-600" x-text="utils.formatCurrency(lavado.comision)"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="lavado.estado === 'completado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="lavado.estado"></span>
                            </td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a :href="`/lavados/${lavado.id}`" class="p-2 text-gray-500 hover:text-brand-blue hover:bg-brand-blue-soft rounded-lg transition" title="Ver detalle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </a>
                                    <button @click="anular(lavado)" x-show="lavado.estado !== 'anulado'" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Anular">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredLavados.length === 0">
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7-7m0 0l-7 7m7-7v10M5 9l7 7m0 0l7-7m-7 7V5"></path></svg>
                            <p>No se encontraron lavados</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Summary -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500">Total lavados</p>
                <p class="font-bold text-gray-900" x-text="filteredLavados.length"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Ingreso total</p>
                <p class="font-bold text-green-600" x-text="utils.formatCurrency(totalIngreso)"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Comisión total</p>
                <p class="font-bold text-amber-600" x-text="utils.formatCurrency(totalComision)"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function lavadosIndex() {
        return {
            search: '',
            empleadoFilter: '',
            tamanoFilter: '',
            estadoFilter: '',
            lavados: [],
            empleados: [],
            stats: { totalHoy: 0, ingresoHoy: 0, comisionHoy: 0, totalMes: 0 },
            
            init() {
                this.lavados = this.$store.lavados.getAll();
                this.empleados = this.$store.empleados.getAll();
                this.updateStats();
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.statsGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
            },
            
            updateStats() {
                this.stats = this.$store.lavados.getStats();
            },
            
            filter() {
                let result = [...this.lavados];
                
                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(l => 
                        l.vehiculoPlaca.toLowerCase().includes(q) ||
                        l.clienteNombre.toLowerCase().includes(q) ||
                        l.empleadoNombre.toLowerCase().includes(q)
                    );
                }
                
                if (this.empleadoFilter) {
                    result = result.filter(l => l.empleadoId == this.empleadoFilter);
                }
                
                if (this.tamanoFilter) {
                    result = result.filter(l => l.vehiculoTamano === this.tamanoFilter);
                }
                
                if (this.estadoFilter) {
                    result = result.filter(l => l.estado === this.estadoFilter);
                }
                
                this.filteredLavados = result;
            },
            
            get filteredLavados() {
                this.filter();
                return this._filtered || [];
            },
            
            set filteredLavados(val) {
                this._filtered = val;
            },
            
            get totalIngreso() {
                return this._filtered?.reduce((sum, l) => sum + (l.estado === 'completado' ? l.precio : 0), 0) || 0;
            },
            
            get totalComision() {
                return this._filtered?.reduce((sum, l) => sum + (l.estado === 'completado' ? l.comision : 0), 0) || 0;
            },
            
            anular(lavado) {
                this.$store.modal.open({
                    title: 'Anular lavado',
                    message: `¿Está seguro de anular el lavado de ${lavado.vehiculoPlaca}?`,
                    variant: 'danger',
                    onConfirm: () => {
                        this.$store.lavados.anular(lavado.id);
                        this.$store.toast.success('Lavado anulado');
                        this.lavados = this.$store.lavados.getAll();
                    }
                });
            }
        }
    }
</script>
@endsection