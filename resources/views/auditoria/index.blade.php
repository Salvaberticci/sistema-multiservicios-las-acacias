@extends('layouts.main')

@section('title', 'Auditoría - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="auditoriaIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Auditoría</h1>
            <p class="text-gray-500 mt-1">Bitácora de acciones y eventos del sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="commandPalette.toggle()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Buscar (Cmd+K)
            </button>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-100 p-4" x-ref="filters">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <select x-model="usuarioFilter" @change="filter()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todos los usuarios</option>
                    <template x-for="user in usuarios" :key="user.id">
                        <option :value="user.id" x-text="user.nombre"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1">Acción</label>
                <select x-model="accionFilter" @change="filter()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todas las acciones</option>
                    <option value="crear">Crear</option>
                    <option value="editar">Editar</option>
                    <option value="anular">Anular</option>
                    <option value="login">Login</option>
                    <option value="exportar">Exportar</option>
                    <option value="acceso">Acceso</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1">Entidad</label>
                <select x-model="entidadFilter" @change="filter()" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todas las entidades</option>
                    <option value="factura">Factura</option>
                    <option value="vehiculo">Vehículo</option>
                    <option value="lavado">Lavado</option>
                    <option value="producto">Producto</option>
                    <option value="usuario">Usuario</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 mb-1">Búsqueda</label>
                <input type="text" x-model="busqueda" @input="filter()" placeholder="Buscar en descripción, usuario o entidad..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden" x-ref="tableContainer">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Acción</th>
                        <th class="px-4 py-3">Entidad</th>
                        <th class="px-4 py-3">Entidad ID</th>
                        <th class="px-4 py-3 text-right">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="log in logs" :key="log.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-500" x-text="utils.formatDate(log.timestamp)"></td>
                            <td class="px-4 py-4 text-sm text-gray-900" x-text="log.usuarioNombre"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="accionClass(log.accion)" x-text="accionLabel(log.accion)"></span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600" x-text="log.entidad"></td>
                            <td class="px-4 py-4 text-sm text-gray-500 truncate" x-text="log.entidadId"></td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/auditoria/${log.id}`" class="p-1 text-gray-500 hover:text-brand-blue hover:bg-brand-blue-soft rounded-lg transition" title="Ver detalle">
                                        <svg class="w-4 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="logs.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p>No se encontraron registros de auditoría</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="logs.length > 0">
            <p class="text-sm text-gray-500">Mostrando <span x-text="paginatedStart"></span> a <span x-text="paginatedEnd"></span> de <span x-text="totalLogs"></span> registros</p>
            <div class="flex gap-2">
                <button @click="prevPage()" :disabled="page === 1" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <template x-for="p in visiblePages" :key="p">
                    <button @click="goPage(p)" :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition', page === p ? 'bg-brand-blue text-white' : 'text-gray-600 hover:bg-gray-100']" x-text="p"></button>
                </template>
                <button @click="nextPage()" :disabled="page === totalPages" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function auditoriaIndex() {
        return {
            logs: [],
            usuarios: [],
            accionFilter: '',
            entidadFilter: '',
            usuarioFilter: '',
            busqueda: '',
            page: 1,
            perPage: 20,
            totalLogs: 0,

            get totalPages() {
                return Math.max(1, Math.ceil(this.totalLogs / this.perPage));
            },
            get paginatedStart() {
                return this.totalLogs === 0 ? 0 : (this.page - 1) * this.perPage + 1;
            },
            get paginatedEnd() {
                return Math.min(this.page * this.perPage, this.totalLogs);
            },
            get visiblePages() {
                const total = this.totalPages;
                const pages = [];
                for (let p = Math.max(1, this.page - 2); p <= Math.min(total, this.page + 2); p++) pages.push(p);
                return pages;
            },

            init() {
                this.usuarios = this.$store.users.getAll().map(u => ({ id: u.id, nombre: u.name }));
                this.loadLogs();
                
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from(this.$refs.filters, { opacity: 0, y: -10, duration: 0.3, ease: 'power2.out' });
                gsap.from(this.$refs.tableContainer, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.1 });
                
                setTimeout(() => {
                    const rows = this.$refs.tableContainer.querySelectorAll('tbody tr');
                    gsap.from(rows, { opacity: 0, y: 12, duration: 0.3, stagger: 0.04, ease: 'power2.out' });
                }, 100);
            },
            
            loadLogs() {
                const allLogs = this.$store.auditoria.getAll({
                    usuario: this.usuarioFilter || undefined,
                    accion: this.accionFilter || undefined,
                    entidad: this.entidadFilter || undefined,
                    busqueda: this.busqueda || undefined
                });
                this.logs = allLogs.slice((this.page - 1) * this.perPage, this.page * this.perPage);
                this.totalLogs = allLogs.length;
            },
            
            filter() {
                this.page = 1;
                this.loadLogs();
            },

            nextPage() {
                if (this.page < this.totalPages) {
                    this.page++;
                    this.loadLogs();
                }
            },

            prevPage() {
                if (this.page > 1) {
                    this.page--;
                    this.loadLogs();
                }
            },

            goPage(p) {
                const page = Math.max(1, Math.min(Number(p), this.totalPages));
                if (page === this.page) return;
                this.page = page;
                this.loadLogs();
            },
            
            accionClass(accion) {
                const classes = {
                    crear: 'bg-blue-100 text-blue-800',
                    editar: 'bg-amber-100 text-amber-800',
                    anular: 'bg-red-100 text-red-800',
                    login: 'bg-green-100 text-green-800',
                    exportar: 'bg-cyan-100 text-cyan-800',
                    acceso: 'bg-indigo-100 text-indigo-800'
                };
                return classes[accion] || classes.borrar || 'text-gray-500';
            },
            
            accionLabel(accion) {
                const labels = {
                    crear: 'Crear',
                    editar: 'Editar',
                    anular: 'Anular',
                    login: 'Login',
                    exportar: 'Exportar',
                    acceso: 'Acceso'
                };
                return labels[accion] || accion;
            }
        }
    }
</script>
@endsection