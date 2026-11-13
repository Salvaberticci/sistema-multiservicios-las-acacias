@extends('layouts.main')

@section('title', 'Seguridad - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="seguridadIndex()" x-show="auth.isAuthenticated">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Seguridad</h1>
            <p class="text-gray-500 mt-1">Gestión de usuarios y permisos</p>
        </div>
        <a href="/seguridad/create" class="btn-primary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Nuevo usuario
        </a>
    </div>
    
    <!-- Roles info -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-ref="rolesGrid">
        <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
            <h3 class="text-sm font-medium text-brand-blue mb-1">Administrador</h3>
            <p class="text-xs text-gray-600">Acceso total al sistema</p>
        </div>
        <div class="bg-cyan-50 rounded-xl border border-cyan-100 p-4">
            <h3 class="text-sm font-medium text-cyan-700 mb-1">Operador</h3>
            <p class="text-xs text-gray-600">Gestión completa de operaciones</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-100 p-4">
            <h3 class="text-sm font-medium text-green-700 mb-1">Cajero</h3>
            <p class="text-xs text-gray-600">Facturación y cobros</p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-100 p-4">
            <h3 class="text-sm font-medium text-amber-700 mb-1">Auditor</h3>
            <p class="text-xs text-gray-600">Solo auditoría y reportes</p>
        </div>
    </div>
    
    <!-- Users table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Rol</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-medium text-sm" x-text="user.name.charAt(0)"></div>
                                    <span class="font-medium text-gray-900" x-text="user.name"></span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600" x-text="user.email"></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="roleClass(user.role)" x-text="roleLabel(user.role)"></span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="user.active ? 'Activo' : 'Inactivo'"></span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/seguridad/${user.id}/edit`" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="toggleStatus(user)" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Activar/Desactivar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="users.length === 0">
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2z"></path></svg>
                            <p>No hay usuarios registrados</p>
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
    function seguridadIndex() {
        return {
            users: [],
            
            init() {
                this.users = this.$store.users.getAll();
                
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from(this.$refs.rolesGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
                gsap.from('table', { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.2 });
            },
            
            roleClass(role) {
                const classes = {
                    admin: 'bg-brand-blue-soft text-brand-blue',
                    operador: 'bg-cyan-100 text-cyan-800',
                    cajero: 'bg-green-100 text-green-800',
                    auditor: 'bg-amber-100 text-amber-800'
                };
                return classes[role] || 'text-gray-500';
            },
            
            roleLabel(role) {
                const labels = {
                    admin: 'Administrador',
                    operador: 'Operador',
                    cajero: 'Cajero',
                    auditor: 'Auditor'
                };
                return labels[role] || role;
            },
            
            toggleStatus(user) {
                user.active = !user.active;
                this.$store.users.save();
                this.$store.toast.success(user.active ? 'Usuario activado' : 'Usuario desactivado');
            }
        }
    }
</script>
@endsection