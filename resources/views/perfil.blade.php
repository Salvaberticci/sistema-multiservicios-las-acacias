@extends('layouts.main')

@section('title', 'Mi perfil - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="perfil()" x-show="auth.isAuthenticated">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mi perfil</h1>
        </div>
        <div class="flex items-center gap-3">
            <button @click="commandPalette.toggle()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Buscar (Cmd+K)
            </button>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 p-6 max-w-md mx-auto">
        <div class="text-center mb-8">
            <div class="w-24 h-24 mx-auto rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-medium text-lg mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01m2.684 1.951a7.968 7.968 0 011.245 2.605l-2.35 2.193m-2.091.616a7.966 7.966 0 00-3.158 1.56l.694-3.856m6.859-1.028a8 8 0 001.061-1.522l-1.758-4.293m2.197.558a7.988 7.988 0 010 2.388l-1.627.536m1.5 0a7.99 7.99 0 00-2.028.882l-.355.968m7.532 3.388l.387 2.152m5.8c.756-.966 1.473-2.021 1.937-3.176a7.974 7.974 0 011.194-.627l.175-2.05m-11.332.749a7.989 7.989 0 01-.561.218l-.527 1.752m6.369-.896a7.99 7.99 0 01-.53-.293l-.742-2.356m6.278.817a7.983 7.983 0 010 2.441l.742.443m6.958-2.058l.384-1.089"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900" x-text="auth.user?.name"></h2>
            <p class="text-gray-500">@x-text="auth.user?.email"></p>
        </div>
        
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 font-medium text-xs uppercase">Rol</dt>
                <dd class="text-gray-900 font-medium" :class="roleClass(auth.user?.role)" x-text="roleLabel(auth.user?.role)"></dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium text-xs uppercase">Fecha de registro</dt>
                <dd class="text-gray-600 font-small" x-text="utils.formatDate(auth.user?.createdAt || new Date())"></dd>
            </div>
        </dl>
        
        <hr class="my-6 border-gray-100">
        
        <a href="/ajustes" class="w-full text-center btn-primary py-3 rounded-lg font-medium text-lg hover:bg-brand-blue-soft transition">
            Actualizar configuración
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function perfil() {
        return {
            init() {
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from('main > div', { opacity: 0, y: 20, duration: 0.4, ease: 'power2.out' });
            },
            
            roleClass(role) {
                const classes = {
                    admin: 'text-brand-blue font-medium',
                    operador: 'text-cyan-800 font-medium',
                    cajero: 'text-green-800 font-medium',
                    auditor: 'text-amber-800 font-medium'
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
            }
        }
    }
</script>
@endsection