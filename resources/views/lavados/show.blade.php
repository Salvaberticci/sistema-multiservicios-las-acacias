@extends('layouts.main')

@section('title', 'Detalle de lavado - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="lavadosShow()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="/lavados" class="inline-flex items-center gap-2 text-sm text-brand-blue hover:text-brand-blue-dark font-medium mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Lavado <span class="font-mono text-brand-blue" x-text="lavado?.vehiculoPlaca || ''"></span></h1>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/lavados/${lavado?.id}/edit`" x-show="lavado?.estado === 'completado'" class="btn-secondary px-4 py-2">Editar</a>
            <button @click="anular(lavado)" x-show="lavado?.estado === 'completado'" class="btn-secondary px-4 py-2 text-red-600 hover:bg-red-50">Anular</button>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</p>
                    <p class="font-mono text-gray-900" x-text="lavado?.vehiculoPlaca"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</p>
                    <p class="font-medium text-gray-900" x-text="lavado?.clienteNombre"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño</p>
                    <p class="font-medium text-gray-900 capitalize" x-text="lavado?.vehiculoTamano"></p>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="p-6 border-b border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900" x-text="utils.formatCurrency(lavado?.precio)"></p>
                <p class="text-sm text-gray-500">Precio</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-amber-600" x-text="utils.formatCurrency(lavado?.comision)"></p>
                <p class="text-sm text-gray-500">Comisión</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900" x-text="lavado?.empleadoNombre"></p>
                <p class="text-sm text-gray-500">Responsable</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-gray-900" x-text="utils.formatDate(lavado?.fecha)"></p>
                <p class="text-sm text-gray-500">Fecha</p>
            </div>
        </div>
        
        <!-- Observaciones -->
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-sm font-medium text-gray-700 mb-2">Observaciones</h2>
            <p class="text-gray-600" x-text="lavado?.observaciones || 'Sin observaciones'"></p>
        </div>
        
        <!-- Estado -->
        <div class="p-6">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Estado del registro:</span>
                <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full" :class="lavado?.estado === 'completado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="lavado?.estado"></span>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function lavadosShow() {
        return {
            lavado: null,
            
            init() {
                const id = window.location.pathname.split('/').pop();
                this.lavado = this.$store.lavados.getById(id);
                
                if (!this.lavado) {
                    this.$store.toast.error('Lavado no encontrado');
                    window.location.href = '/lavados';
                    return;
                }
                
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from('main > div', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
            },
            
            anular(lavado) {
                this.$store.modal.open({
                    title: 'Anular lavado',
                    message: `¿Está seguro de anular el lavado de ${lavado.vehiculoPlaca}?`,
                    variant: 'danger',
                    onConfirm: () => {
                        this.$store.lavados.anular(lavado.id);
                        this.$store.toast.success('Lavado anulado');
                        window.location.href = '/lavados';
                    }
                });
            }
        }
    }
</script>
@endsection