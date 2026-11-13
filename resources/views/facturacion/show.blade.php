@extends('layouts.main')

@section('title', 'Factura {{ $id }} - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="facturaShow('{{ $id }}')">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6" x-ref="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Factura <span class="text-brand-blue" x-text="factura?.numero"></span></h1>
            <p class="text-gray-500 mt-1" x-text="utils.formatDate(factura?.fecha)"></p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex px-3 py-1.5 text-sm font-medium rounded-full" :class="estadoClass(factura?.estado)" x-text="estadoLabel(factura?.estado)"></span>
            <template x-if="factura?.estado !== 'anulada'">
                <a :href="`/facturacion/${factura.id}/edit`" x-show="factura?.estado === 'borrador'" class="btn-secondary px-4 py-2">Editar</a>
                <button @click="anular()" x-show="factura?.estado !== 'anulada'" class="btn-danger px-4 py-2">Anular</button>
            </template>
            <a href="/facturacion" class="btn-secondary px-4 py-2">Volver</a>
        </div>
    </div>
    
    <!-- Factura Card -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden" x-ref="card">
        <!-- Header Info -->
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</p>
                    <p class="font-medium text-gray-900" x-text="factura?.clienteNombre"></p>
                    <p class="text-sm text-gray-500" x-text="factura?.clienteTelefono"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</p>
                    <p class="font-medium text-gray-900" x-text="utils.formatDate(factura?.fecha)"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Método pago</p>
                    <p class="font-medium text-gray-900 capitalize" x-text="factura?.metodoPago"></p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-brand-blue" x-text="utils.formatCurrency(factura?.total)"></p>
                </div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-100">
            <nav class="flex gap-1 px-6" role="tablist">
                <button @click="activeTab = 'detalle'" :class="tabClass('detalle')" role="tab">Detalle</button>
                <button @click="activeTab = 'pagos'" :class="tabClass('pagos')" role="tab">Pagos</button>
                <button @click="activeTab = 'historial'" :class="tabClass('historial')" role="tab">Historial</button>
                <button @click="descargarPDF()" :class="tabClass('pdf')" role="tab">PDF</button>
            </nav>
        </div>
        
        <!-- Tab Panels -->
        <div class="p-6">
            <!-- Detalle -->
            <div x-show="activeTab === 'detalle'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-3 px-4">Servicio</th>
                                <th class="pb-3 px-4 text-center">Cant.</th>
                                <th class="pb-3 px-4 text-right">Precio unit.</th>
                                <th class="pb-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="linea in factura?.lineas" :key="linea.id">
                                <tr>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-gray-900" x-text="linea.servicio"></p>
                                    </td>
                                    <td class="py-3 px-4 text-center text-sm text-gray-600" x-text="linea.cantidad"></td>
                                    <td class="py-3 px-4 text-right text-sm text-gray-600" x-text="utils.formatCurrency(linea.precioUnitario)"></td>
                                    <td class="py-3 px-4 text-right font-medium text-gray-900" x-text="utils.formatCurrency(linea.subtotal)"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right font-medium text-gray-600">Subtotal</td>
                                <td class="py-3 px-4 text-right font-medium text-gray-900" x-text="utils.formatCurrency(factura?.subtotal)"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-3 px-4 text-right font-medium text-gray-600">IVA (21%)</td>
                                <td class="py-3 px-4 text-right font-medium text-gray-900" x-text="utils.formatCurrency(factura?.iva)"></td>
                            </tr>
                            <tr class="border-t-2 border-gray-200">
                                <td colspan="3" class="py-3 px-4 text-right font-bold text-gray-900">Total</td>
                                <td class="py-3 px-4 text-right text-xl font-bold text-brand-blue" x-text="utils.formatCurrency(factura?.total)"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Pagos -->
            <div x-show="activeTab === 'pagos'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="space-y-4">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-700">Pagado con</p>
                                <p class="font-medium text-green-900 capitalize" x-text="factura?.metodoPago"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-green-700">Monto</p>
                                <p class="font-bold text-green-900 text-xl" x-text="utils.formatCurrency(factura?.montoRecibido || factura?.total)"></p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-4 text-center" x-show="factura?.cambio > 0">
                            <div class="bg-white rounded-lg p-3">
                                <p class="text-xs text-green-600">Cambio</p>
                                <p class="font-bold text-green-900" x-text="utils.formatCurrency(factura?.cambio)"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="!factura?.montoRecibido" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <p>No hay información de pago detallada</p>
                    </div>
                </div>
            </div>
            
            <!-- Historial -->
            <div x-show="activeTab === 'historial'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Factura creada</p>
                                <p class="text-sm text-gray-500" x-text="utils.formatDate(factura?.fecha)"></p>
                            </div>
                        </div>
                    </div>
                    
                    <template x-for="log in historial" :key="log.id">
                        <div class="bg-white border border-gray-100 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div :class="['w-8 h-8 rounded-full flex items-center justify-center', log.accion === 'crear' ? 'bg-blue-100 text-blue-600' : log.accion === 'editar' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600']">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-html="logIcon(log.accion)"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900" x-text="logLabel(log.accion)"></p>
                                    <p class="text-sm text-gray-500" x-text="utils.formatDate(log.timestamp) + ' por ' + log.usuarioNombre"></p>
                                </div>
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded" x-text="log.entidad"></span>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="historial.length === 0" class="text-center py-8 text-gray-500">
                        <p>Sin historial adicional</p>
                    </div>
                </div>
            </div>
            
            <!-- PDF Placeholder -->
            <div x-show="activeTab === 'pdf'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100" class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Generación de PDF</h3>
                <p class="text-gray-500 mb-6">La generación de PDF requiere integración con backend (DomPDF, Snappy, etc.)</p>
                <button @click="descargarPDF()" class="btn-primary px-6 py-2.5">Generar PDF (simulado)</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function facturaShow(id) {
        return {
            factura: {},
            historial: [],
            activeTab: 'detalle',
            
            init() {
                this.loadFactura(id);
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.header, { opacity: 0, y: -10, duration: 0.3, ease: 'power2.out' });
                gsap.from(this.$refs.card, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.1 });
            },
            
            loadFactura(id) {
                this.factura = this.$store.facturas.getById(id) || {};
                if (!this.factura.id) {
                    this.$store.toast.error('Factura no encontrada');
                    window.location.href = '/facturacion';
                    return;
                }
                this.historial = this.$store.auditoria.getAll({ entidad: 'factura', entidadId: id });
            },
            
            tabClass(tab) {
                return `px-4 py-2 text-sm font-medium rounded-t-lg transition ${this.activeTab === tab ? 'text-brand-blue border-b-2 border-brand-blue' : 'text-gray-500 hover:text-gray-700'}`;
            },
            
            estadoClass(estado) {
                const classes = {
                    borrador: 'bg-gray-100 text-gray-800',
                    pendiente: 'bg-amber-100 text-amber-800',
                    pagada: 'bg-green-100 text-green-800',
                    anulada: 'bg-red-100 text-red-800'
                };
                return classes[estado] || classes.borrador;
            },
            
            estadoLabel(estado) {
                const labels = {
                    borrador: 'Borrador',
                    pendiente: 'Pendiente',
                    pagada: 'Pagada',
                    anulada: 'Anulada'
                };
                return labels[estado] || estado;
            },
            
            anular() {
                this.$store.modal.open({
                    title: 'Anular factura',
                    message: `¿Está seguro de anular la factura ${this.factura.numero}? Esta acción no se puede deshacer.`,
                    variant: 'danger',
                    onConfirm: () => {
                        this.$store.facturas.anular(this.factura.id);
                        this.$store.toast.success('Factura anulada');
                        this.loadFactura(this.factura.id);
                    }
                });
            },
            
            descargarPDF() {
                this.$store.toast.info('Generando PDF... (simulado)');
                // Simulate PDF generation
                setTimeout(() => {
                    this.$store.toast.success('PDF generado (simulado)');
                }, 1000);
            },
            
            logIcon(accion) {
                const icons = {
                    crear: 'M12 4v16m8-8H4',
                    editar: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                    anular: 'M6 18L18 6M6 6l12 12'
                };
                return icons[accion] || icons.crear;
            },
            
            logLabel(accion) {
                const labels = {
                    crear: 'Factura creada',
                    editar: 'Factura editada',
                    anular: 'Factura anulada'
                };
                return labels[accion] || accion;
            }
        }
    }
</script>
@endsection