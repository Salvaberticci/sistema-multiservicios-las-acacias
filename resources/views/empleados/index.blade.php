@extends('layouts.main')

@section('title', 'Empleados - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="empleadosIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Empleados</h1>
            <p class="text-gray-500 mt-1">Personal de lavado y trabajadores disponibles</p>
        </div>
        <a href="/empleados/create" class="btn-primary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Nuevo empleado
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-ref="statsGrid">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-brand-blue" x-text="stats.activos"></p>
            <p class="text-sm text-gray-500">Activos</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-green-600" x-text="stats.manana"></p>
            <p class="text-sm text-gray-500">Turno mañana</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-cyan-600" x-text="stats.tarde"></p>
            <p class="text-sm text-gray-500">Turno tarde</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900" x-text="stats.total"></p>
            <p class="text-sm text-gray-500">Total registrados</p>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" x-model="search" @input="filter()" placeholder="Buscar nombre, teléfono o email..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
            </div>

            <select x-model="turnoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                <option value="">Todos los turnos</option>
                <option value="mañana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="completo">Completo</option>
            </select>

            <select x-model="estadoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Empleado</th>
                        <th class="px-4 py-3">Contacto</th>
                        <th class="px-4 py-3">Cargo</th>
                        <th class="px-4 py-3">Turno</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="empleado in filteredEmpleados" :key="empleado.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-semibold text-sm" x-text="iniciales(empleado.nombre)"></div>
                                    <div>
                                        <p class="font-medium text-gray-900" x-text="empleado.nombre"></p>
                                        <p class="text-xs text-gray-500" x-text="'Desde ' + utils.formatDateShort(empleado.fechaIngreso)"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-900" x-text="empleado.telefono"></p>
                                <p class="text-xs text-gray-500" x-text="empleado.email || 'Sin email'"></p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700" x-text="empleado.cargo"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="turnoClass(empleado.turno)" x-text="empleado.turno"></span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="empleado.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="empleado.activo ? 'Activo' : 'Inactivo'"></span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/empleados/${empleado.id}/edit`" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="toggleStatus(empleado)" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Activar/Desactivar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredEmpleados.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"></path></svg>
                            <p>No hay empleados que coincidan con el filtro</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function empleadosIndex() {
        return {
            empleados: [],
            filteredEmpleados: [],
            search: '',
            turnoFilter: '',
            estadoFilter: '',
            stats: { total: 0, activos: 0, inactivos: 0, manana: 0, tarde: 0 },

            init() {
                this.empleados = this.$store.empleados.getAll();
                this.filter();
                this.stats = this.$store.empleados.getStats();
                this.$nextTick(() => this.animateEntrance());
            },

            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.statsGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
            },

            filter() {
                let result = [...this.empleados];

                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(e =>
                        e.nombre.toLowerCase().includes(q) ||
                        (e.telefono || '').toLowerCase().includes(q) ||
                        (e.email || '').toLowerCase().includes(q)
                    );
                }

                if (this.turnoFilter) {
                    result = result.filter(e => e.turno === this.turnoFilter);
                }

                if (this.estadoFilter) {
                    result = result.filter(e => this.estadoFilter === 'activo' ? e.activo : !e.activo);
                }

                this.filteredEmpleados = result;
            },

            iniciales(nombre) {
                return nombre.split(' ').map(p => p.charAt(0)).slice(0, 2).join('').toUpperCase();
            },

            turnoClass(turno) {
                const classes = {
                    'mañana': 'bg-amber-100 text-amber-800',
                    'tarde': 'bg-cyan-100 text-cyan-800',
                    'completo': 'bg-brand-blue-soft text-brand-blue'
                };
                return classes[turno] || 'bg-gray-100 text-gray-700';
            },

            toggleStatus(empleado) {
                const activo = this.$store.empleados.toggleStatus(empleado.id);
                empleado.activo = activo;
                this.stats = this.$store.empleados.getStats();
                this.$store.toast.success(activo ? 'Empleado activado' : 'Empleado desactivado');
                this.filter();
            }
        }
    }
</script>
@endsection
