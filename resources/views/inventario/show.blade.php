@extends('layouts.main')

@section('title', 'Producto {{ $id }} - Multiservicios Las Acacias')

@section('content')
<div class="max-w-4xl mx-auto" x-data="inventarioShow('{{ $id }}')">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Producto</h1>
            <p class="text-gray-500 mt-1" x-text="producto?.nombre"></p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/inventario/${producto.id}/edit`" class="btn-secondary px-4 py-2">Editar</a>
            <a href="/inventario" class="btn-secondary px-4 py-2">Volver</a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden" x-ref="card">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Código</p>
                    <p class="font-mono text-gray-900" x-text="producto?.codigo"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</p>
                    <p class="font-medium text-gray-900" x-text="producto?.categoria"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</p>
                    <p class="font-medium text-gray-900" x-text="producto?.unidad"></p>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="p-6 border-b border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-3xl font-bold" :class="producto?.stock === 0 ? 'text-red-600' : producto?.stock <= producto?.stockMin ? 'text-amber-600' : 'text-gray-900'" x-text="producto?.stock"></p>
                <p class="text-sm text-gray-500">Stock actual</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="producto?.stockMin"></p>
                <p class="text-sm text-gray-500">Stock mínimo</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600" x-text="utils.formatCurrency(producto?.precioVenta)"></p>
                <p class="text-sm text-gray-500">Precio venta</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="utils.formatCurrency(producto?.precioCompra)"></p>
                <p class="text-sm text-gray-500">Precio compra</p>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-100">
            <nav class="flex gap-1 px-6" role="tablist">
                <button @click="activeTab = 'detalle'" :class="tabClass('detalle')" role="tab">Detalle</button>
                <button @click="activeTab = 'movimientos'" :class="tabClass('movimientos')" role="tab">Movimientos</button>
            </nav>
        </div>
        
        <div class="p-6">
            <div x-show="activeTab === 'detalle'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-sm text-gray-500">Proveedor</p><p class="font-medium text-gray-900" x-text="getProveedorNombre(producto?.proveedorId)"></p></div>
                        <div><p class="text-sm text-gray-500">Ubicación</p><p class="font-medium text-gray-900" x-text="producto?.ubicacion || 'No especificada'"></p></div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-900 mb-2">Ajuste rápido de stock</p>
                        <div class="flex gap-3">
                            <input type="number" x-model.number="ajusteCantidad" class="w-24 px-3 py-2 bg-white border border-gray-200 rounded-lg text-center text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Cantidad">
                            <select x-model="ajusteTipo" class="w-32 px-3 py-2 bg-white border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="entrada">Entrada (+)</option>
                                <option value="salida">Salida (-)</option>
                                <option value="ajuste">Ajuste</option>
                            </select>
                            <button @click="aplicarAjuste()" class="btn-primary px-4 py-2">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div x-show="activeTab === 'movimientos'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3 text-center">Cantidad</th>
                                <th class="px-4 py-3">Responsable</th>
                                <th class="px-4 py-3">Referencia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="mov in movimientos" :key="mov.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(mov.fecha)"></td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="mov.tipo === 'entrada' ? 'bg-green-100 text-green-800' : mov.tipo === 'salida' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'" x-text="mov.tipo"></span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span :class="mov.tipo === 'entrada' ? 'text-green-600' : mov.tipo === 'salida' ? 'text-red-600' : 'text-amber-600'" class="font-medium" x-text="(mov.tipo === 'salida' ? '-' : '+') + mov.cantidad"></span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600" x-text="getResponsableNombre(mov.responsableId)"></td>
                                    <td class="px-4 py-4 text-sm text-gray-500" x-text="mov.referencia"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function inventarioShow(id) {
        return {
            producto: {},
            movimientos: [],
            activeTab: 'detalle',
            ajusteCantidad: 1,
            ajusteTipo: 'entrada',
            
            init() {
                this.loadProducto(id);
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.card, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out' });
            },
            
            loadProducto(id) {
                this.producto = this.$store.inventario.getProducto(id) || {};
                if (!this.producto.id) {
                    this.$store.toast.error('Producto no encontrado');
                    window.location.href = '/inventario';
                    return;
                }
                this.movimientos = this.$store.inventario.getMovimientos(id);
            },
            
            tabClass(tab) {
                return `px-4 py-2 text-sm font-medium rounded-t-lg transition ${this.activeTab === tab ? 'text-brand-blue border-b-2 border-brand-blue' : 'text-gray-500 hover:text-gray-700'}`;
            },
            
            getProveedorNombre(id) {
                const provs = { 1: 'Distribuidora Limpieza SL', 2: 'Ceras Premium SA', 3: 'Accesorios Auto SL', 4: 'Químicos Industriales', 5: 'Herramientas Pro' };
                return provs[id] || 'Sin proveedor';
            },
            
            getResponsableNombre(id) {
                const users = { 1: 'Admin', 2: 'Operador', 3: 'Cajero', 4: 'Auditor' };
                return users[id] || 'Sistema';
            },
            
            aplicarAjuste() {
                this.$store.inventario.addMovimiento({
                    tipo: this.ajusteTipo,
                    productoId: this.producto.id,
                    cantidad: this.ajusteCantidad,
                    responsableId: this.$store.auth.user?.id || 1,
                    referencia: 'Ajuste manual'
                });
                this.$store.toast.success('Stock ajustado');
                this.loadProducto(this.producto.id);
                this.ajusteCantidad = 1;
            }
        }
    }
</script>
@endsection