@extends('layouts.main')

@section('title', 'Inventario - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="inventarioIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventario</h1>
            <p class="text-gray-500 mt-1">Productos, insumos y movimientos de stock</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="$store.commandPalette.toggle()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Buscar (Cmd+K)
            </button>
            <a href="/inventario/create" class="btn-primary px-4 py-2.5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo producto
            </a>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-ref="statsGrid">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Total productos</p>
            <p class="text-2xl font-bold text-gray-900" x-text="stats.totalProductos"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Stock bajo</p>
            <p class="text-2xl font-bold text-amber-600" x-text="stats.stockBajo"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Sin stock</p>
            <p class="text-2xl font-bold text-red-600" x-text="stats.sinStock"></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Valor stock</p>
            <p class="text-2xl font-bold text-brand-blue" x-text="utils.formatCurrency(stats.valorStock)"></p>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100">
            <nav class="flex gap-1 px-6" role="tablist">
                <button @click="activeTab = 'productos'" :class="tabClass('productos')" role="tab">Productos</button>
                <button @click="activeTab = 'movimientos'" :class="tabClass('movimientos')" role="tab">Movimientos</button>
            </nav>
        </div>
        
        <!-- Productos Tab -->
        <div x-show="activeTab === 'productos'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100" class="p-0">
            <!-- Toolbar -->
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" x-model="search" @input="filter()" placeholder="Buscar producto..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
                </div>
                <select x-model="categoriaFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todas las categorías</option>
                    <template x-for="cat in categorias" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" x-model="stockBajoFilter" @change="filter()" class="w-4 h-4 text-brand-blue border-gray-300 rounded focus:ring-brand-blue">
                    Solo stock bajo
                </label>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">Código</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Categoría</th>
                            <th class="px-4 py-3 text-center">Stock</th>
                            <th class="px-4 py-3 text-center">Mín</th>
                            <th class="px-4 py-3 text-right">Precio venta</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="prod in filteredProductos" :key="prod.id">
                            <tr class="hover:bg-gray-50 transition-colors" :class="prod.stock <= prod.stockMin ? 'bg-amber-50' : ''">
                                <td class="px-4 py-4">
                                    <span class="font-mono text-sm text-gray-600" x-text="prod.codigo"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-gray-900" x-text="prod.nombre"></p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700" x-text="prod.categoria"></span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span :class="prod.stock === 0 ? 'text-red-600' : prod.stock <= prod.stockMin ? 'text-amber-600' : 'text-gray-900'" class="font-medium" x-text="prod.stock + ' ' + prod.unidad"></span>
                                </td>
                                <td class="px-4 py-4 text-center text-sm text-gray-500" x-text="prod.stockMin + ' ' + prod.unidad"></td>
                                <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="utils.formatCurrency(prod.precioVenta)"></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="ajusteRapido(prod)" class="p-2 text-gray-500 hover:text-brand-blue hover:bg-brand-blue-soft rounded-lg transition" title="Ajuste rápido">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        </button>
                                        <a :href="`/inventario/${prod.id}`" class="p-2 text-gray-500 hover:text-brand-blue hover:bg-brand-blue-soft rounded-lg transition" title="Ver detalle">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </a>
                                        <a :href="`/inventario/${prod.id}/edit`" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredProductos.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <p>No se encontraron productos</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Movimientos Tab -->
        <div x-show="activeTab === 'movimientos'" x-transition:enter="transition ease-out duration-150" x-transition:leave="transition ease-in duration-100" class="p-0">
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" x-model="movSearch" @input="filterMovimientos()" placeholder="Buscar movimientos..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
                </div>
                <select x-model="movTipoFilter" @change="filterMovimientos()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todos los tipos</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                    <option value="ajuste">Ajuste</option>
                </select>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-center">Cantidad</th>
                            <th class="px-4 py-3">Responsable</th>
                            <th class="px-4 py-3">Referencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="mov in filteredMovimientos" :key="mov.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(mov.fecha)"></td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="mov.tipo === 'entrada' ? 'bg-green-100 text-green-800' : mov.tipo === 'salida' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'" x-text="mov.tipo"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-gray-900" x-text="getProductoNombre(mov.productoId)"></p>
                                    <p class="text-xs text-gray-500" x-text="getProductoCodigo(mov.productoId)"></p>
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
@endsection

@section('scripts')
<script>
    function inventarioIndex() {
        return {
            activeTab: 'productos',
            search: '',
            categoriaFilter: '',
            stockBajoFilter: false,
            movSearch: '',
            movTipoFilter: '',
            productos: [],
            filteredProductos: [],
            movimientos: [],
            filteredMovimientos: [],
            categorias: [],
            stats: { totalProductos: 0, stockBajo: 0, sinStock: 0, valorStock: 0 },
            
            init() {
                this.productos = this.$store.inventario.getProductos();
                this.movimientos = this.$store.inventario.getMovimientos();
                this.categorias = [...new Set(this.productos.map(p => p.categoria))].sort();
                this.filter();
                this.filterMovimientos();
                this.updateStats();
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.statsGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
            },
            
            updateStats() {
                const s = this.$store.inventario.getStats();
                this.stats = { ...s };
            },
            
            tabClass(tab) {
                return `px-4 py-2 text-sm font-medium rounded-t-lg transition ${this.activeTab === tab ? 'text-brand-blue border-b-2 border-brand-blue' : 'text-gray-500 hover:text-gray-700'}`;
            },
            
            filter() {
                let result = [...this.productos];
                
                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(p => 
                        p.nombre.toLowerCase().includes(q) ||
                        p.codigo.toLowerCase().includes(q)
                    );
                }
                
                if (this.categoriaFilter) {
                    result = result.filter(p => p.categoria === this.categoriaFilter);
                }
                
                if (this.stockBajoFilter) {
                    result = result.filter(p => p.stock <= p.stockMin);
                }
                
                this.filteredProductos = result;
            },
            
            filterMovimientos() {
                let result = [...this.movimientos];
                
                if (this.movSearch) {
                    const q = this.movSearch.toLowerCase();
                    result = result.filter(m => 
                        this.getProductoNombre(m.productoId).toLowerCase().includes(q) ||
                        m.referencia.toLowerCase().includes(q)
                    );
                }
                
                if (this.movTipoFilter) {
                    result = result.filter(m => m.tipo === this.movTipoFilter);
                }
                
                this.filteredMovimientos = result;
            },
            
            getProductoNombre(id) {
                return this.productos.find(p => p.id === id)?.nombre || 'Desconocido';
            },
            
            getProductoCodigo(id) {
                return this.productos.find(p => p.id === id)?.codigo || '';
            },
            
            getResponsableNombre(id) {
                const users = { 1: 'Admin', 2: 'Operador', 3: 'Cajero', 4: 'Auditor' };
                return users[id] || 'Sistema';
            },
            
            ajusteRapido(prod) {
                this.$store.modal.open({
                    title: 'Ajuste rápido: ' + prod.nombre,
                    message: `Stock actual: ${prod.stock} ${prod.unidad}. Stock mínimo: ${prod.stockMin} ${prod.unidad}.`,
                    variant: 'primary',
                    onConfirm: () => {
                        // Would open a form for adjustment
                        this.$store.toast.info('Función de ajuste rápido - pendiente');
                    }
                });
            }
        }
    }
</script>
@endsection