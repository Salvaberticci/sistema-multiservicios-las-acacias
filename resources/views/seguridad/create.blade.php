@extends('layouts.main')

@section('title', 'Crear usuario - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="seguridadCreate()" x-show="auth.isAuthenticated">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Crear usuario</h1>
            <p class="text-gray-500 mt-1">Registrar nuevo usuario en el sistema</p>
        </div>
        <a href="/seguridad" class="btn-secondary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Cancelar
        </a>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 p-6" x-show="!saving">
        <form @submit.prevent="saveUser">
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña (dejar vacío para mantener actual)</label>
                <input type="password" x-model="user.password" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition" placeholder="Dejar vacío para mantener la actual">
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="btn-primary flex-1 py-2.5 rounded-lg font-medium transition" :disabled="saving">
                    <span x-show="!saving">Guardar usuario</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.562m8.94l-1.898-1.898M12 9v2m0 4v3m2-3v2m-5.5-3A7.953 7.953 0 014 12C4 8.694 5.694 7 8 7m0 0a4.959 4.959 0 011.54 3.033M7 23l-3.5-7m5.618 3.47a1 1 0 01.717.694m14.384.728a1 1 0 01-.717.694M21 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Guardando...
                    </span>
                </button>
                <button type="reset" class="btn-secondary flex-1 py-2.5 rounded-lg font-medium transition">Limpiar</button>
            </div>
        </form>
    </div>
    
    <div x-show="error" class="bg-red-100 border-red-400 rounded-xl p-4 text-red-800" x-ref="errorBox">
        <p class="text-sm" x-text="error"></p>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function seguridadCreate() {
        return {
            user: {
                name: '',
                email: '',
                role: '',
                active: true
            },
            saving: false,
            error: '',
            
            init() {
                this.error = '';
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from('form', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
                gsap.from('.grid', { opacity: 0, y: 10, duration: 0.3, stagger: 0.08, ease: 'power2.out', delay: 0.1 });
            },
            
            saveUser() {
                this.error = '';
                this.saving = true;
                
                if (!this.user.name || !this.user.email || !this.user.role) {
                    this.error = 'Nombre, email y rol son obligatorios';
                    this.saving = false;
                    return;
                }
                
                if (this.$store.users.items.some(u => u.email === this.user.email)) {
                    this.error = 'El correo electrónico ya está registrado';
                    this.saving = false;
                    return;
                }
                
                this.$store.users.create({
                    name: this.user.name,
                    email: this.user.email,
                    role: this.user.role,
                    active: this.user.active
                });
                
                this.$store.toast.success('Usuario creado exitosamente');
                setTimeout(() => { window.location.href = '/seguridad'; }, 600);
            }
        }
    }
</script>
@endsection