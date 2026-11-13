@extends('layouts.main')

@section('title', 'Detalle de auditoría - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="auditoriaShow()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="/auditoria" class="inline-flex items-center gap-2 text-sm text-brand-blue hover:text-brand-blue-dark font-medium mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Registro de auditoría</h1>
        </div>
    </div>
    
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Details Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Información del registro</h2>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="accionClass(log?.accion)" x-text="accionLabel(log?.accion)"></span>
                </div>
                
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">Fecha y hora</dt>
                        <dd class="text-gray-900 font-medium" x-text="log ? utils.formatDate(log.timestamp) : 'Cargando...'"></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">Usuario</dt>
                        <dd class="text-gray-900 font-medium" x-text="log?.usuarioNombre"></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">Entidad</dt>
                        <dd class="text-gray-900 font-medium" x-text="log?.entidad"></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">ID de entidad</dt>
                        <dd class="text-gray-900 font-medium font-mono truncate" x-text="log?.entidadId"></dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500 font-medium text-xs uppercase mb-1 block">IP</dt>
                        <dd class="text-gray-900 font-medium font-mono" x-text="log?.ip"></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Detalles (antes / después) -->
            <div x-show="log?.detalles?.antes || log?.detalles?.despues" class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalles del cambio</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3 border-b border-gray-100 pb-2">Antes</h3>
                        <pre class="text-sm text-gray-600 font-mono break-words" x-text="JSON.stringify(log?.detalles?.antes, null, 2)"></pre>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3 border-b border-gray-100 pb-2">Después</h3>
                        <pre class="text-sm text-gray-600 font-mono break-words" x-text="JSON.stringify(log?.detalles?.despues, null, 2)"></pre>
                    </div>
                </div>
            </div>
            
            <!-- Metadata -->
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadatos</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">User Agent</dt>
                        <dd class="text-gray-900 font-medium truncate max-w-xs" x-text="log?.userAgent"></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium text-xs uppercase">Estado</dt>
                        <dd class="text-gray-900 font-medium" x-text="log?.estado ?? 'Sistema'"></dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Acciones</h3>
                <div class="space-y-2">
                    <a :href="`/auditoria`" class="w-full btn-secondary px-4 py-2 text-sm flex items-center justify-between">
                        <span>Volver al listado</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19l7-7 7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function auditoriaShow() {
        return {
            log: null,
            entities: [],
            
            init() {
                const id = window.location.pathname.split('/').pop();
                this.log = this.$store.auditoria.getById(id);
                this.entities = ['factura', 'vehiculo', 'lavado', 'producto', 'usuario', 'cliente'];
                
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from('main > div > div', { opacity: 0, y: 20, duration: 0.4, stagger: 0.1, ease: 'power2.out' });
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
                return classes[accion] || 'text-gray-500';
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