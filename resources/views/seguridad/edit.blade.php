@extends('layouts.main')

@section('title', 'Editar usuario - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="seguridadEdit()" x-show="auth.isAuthenticated">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar usuario</h1>
            <p class="text-gray-500 mt-1">Modificar datos del usuario y sus permisos</p>
        </div>
        <a href="/seguridad" class="btn-secondary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Cancelar
        </a>
    </div>
    
    <div x-show="user && !saving" class="bg-white rounded-xl border border-gray-100 p-6">
        <form @submit.prevent="updateUser">
            <input type="hidden" x-show="user" :value="user.id">
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" x-model="user.name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition" placeholder="Nombre completo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" x-model="user.email" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition" placeholder="email@ejemplo.com">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                    <select x-model="user.role" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        <option value="">Seleccionar rol</option>
                        <option value="admin">Administrador</option>
                        <option value="operador">Operador</option>
                        <option value="cajero">Cajero</option>
                        <option value="auditor">Auditor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select x-model="user.active" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        <option value="true">Activo</option>
                        <option value="false">Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div class="bg-amber-50 rounded-lg p-4 mb-6 border border-amber-100">
                <p class="text-sm text-amber-800">
                    <strong class="font-medium">Auditoría:</strong> Esta acción quedará registrada en el sistema para fines de seguridad.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 py-2.5 rounded-lg font-medium transition" :disabled="saving">
                    <span x-show="!saving">Guardar cambios</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"></path></svg>
                        Guardando...
                    </span>
                </button>
                <button type="reset" class="btn-secondary flex-1 py-2.5 rounded-lg font-medium transition">Limpiar</button>
            </div>
        </form>
    </div>
    
    <div x-show="!user && !saving" class="bg-white rounded-xl border border-gray-100 p-6 text-center py-12">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-gray-500">No se encontró el usuario</p>
    </div>
    
    <div x-show="error" class="bg-red-100 border-red-400 rounded-xl p-4 text-red-800" x-ref="errorBox">
        <p class="text-sm" x-text="error"></p>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function seguridadEdit() {
        return {
            user: {},
            saving: false,
            error: '',
            
            init() {
                const id = window.location.pathname.split('/').pop();
                this.user = this.$store.users.getById(id) || {};
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from('form', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
            },
            
            updateUser() {
                this.error = '';
                this.saving = true;
                
                if (!this.user.name || !this.user.email || !this.user.role) {
                    this.error = 'Nombre, email y rol son obligatorios';
                    this.saving = false;
                    return;
                }
                
                if (this.$store.users.items.some(u => u.email === this.user.email && u.id != this.user.id)) {
                    this.error = 'El correo electrónico ya está registrado';
                    this.saving = false;
                    return;
                }
                
                this.$store.users.update(this.user.id, {
                    name: this.user.name,
                    email: this.user.email,
                    role: this.user.role,
                    active: this.user.active
                });
                
                this.$store.auditoria.log('editar', 'usuario', this.user.id, {
                    nombre: this.user.name,
                    email: this.user.email,
                    role: this.user.role
                });
                
                this.$store.toast.success('Usuario actualizado exitosamente');
                setTimeout(() => { window.location.href = '/seguridad'; }, 600);
            }
        }
    }
</script>
@endsection