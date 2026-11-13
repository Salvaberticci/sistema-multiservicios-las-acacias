@extends('layouts.main')

@section('title', 'Editar producto - Multiservicios Las Acacias')

@section('content')
<div class="max-w-2xl mx-auto" x-data="inventarioEdit()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar producto</h1>
            <p class="text-gray-500 mt-1">Modificar datos del producto</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/inventario/${producto?.id}`" class="btn-secondary px-4 py-2">Cancelar</a>
        </div>
    </div>
    
    <div x-show="producto && !saving" class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Info básica -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información básica</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" x-model="form.nombre" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                        <input type="text" x-model="form.codigo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                            <select x-model="form.categoria" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="">Seleccionar...</option>
                                <template x-for="cat in categorias" :key="cat">
                                    <option :value="cat" x-text="cat"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidad *</label>
                            <select x-model="form.unidad" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="ud">Unidad (ud)</option>
                                <option value="L">Litro (L)</option>
                                <option value="kg">Kilogramo (kg)</option>
                                <option value="m">Metro (m)</option>
                                <option value="pack">Pack</option>
                                <option value="caja">Caja</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock y precios -->
            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock y precios</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock actual</label>
                        <input type="number" x-model.number="form.stock" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock mínimo</label>
                        <input type="number" x-model.number="form.stockMin" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio compra</label>
                        <input type="number" x-model.number="form.precioCompra" step="0.01" min="0" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio venta *</label>
                        <input type="number" x-model.number="form.precioVenta" step="0.01" min="0" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                </div>
            </div>
            
            <!-- Proveedor y ubicación -->
            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Proveedor y ubicación</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select x-model="form.proveedorId" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                            <option value="">Sin proveedor</option>
                            <template x-for="prov in proveedores" :key="prov.id">
                                <option :value="prov.id" x-text="prov.nombre"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <input type="text" x-model="form.ubicacion" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Ej: Estante A-1">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a :href="`/inventario/${producto.id}`" class="btn-secondary px-6 py-2.5">Cancelar</a>
                <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving">Guardar cambios</button>
            </div>
        </form>
    </div>
    
    <div x-show="!producto && !saving" class="bg-white rounded-xl border border-gray-100 p-6 text-center py-12">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-gray-500">No se encontró el producto</p>
    </div>
</div>

@endsection
@section('scripts')
<script>
    function inventarioEdit() {
        return {
            producto: {},
            form: {},
            categorias: ['Limpieza', 'Ceras', 'Accesorios', 'Químicos', 'Herramientas', 'Papel'],
            proveedores: [
                { id: 1, nombre: 'Distribuidora Limpieza SL' },
                { id: 2, nombre: 'Ceras Premium SA' },
                { id: 3, nombre: 'Accesorios Auto SL' },
                { id: 4, nombre: 'Químicos Industriales' },
                { id: 5, nombre: 'Herramientas Pro' }
            ],
            saving: false,
            
            init() {
                const id = window.location.pathname.split('/').pop();
                this.producto = this.$store.inventario.getProducto(id) || {};
                
                if (this.producto) {
                    this.form = {
                        nombre: this.producto.nombre,
                        codigo: this.producto.codigo,
                        categoria: this.producto.categoria,
                        unidad: this.producto.unidad,
                        stock: this.producto.stock,
                        stockMin: this.producto.stockMin,
                        precioCompra: this.producto.precioCompra,
                        precioVenta: this.producto.precioVenta,
                        proveedorId: this.producto.proveedorId,
                        ubicacion: this.producto.ubicacion
                    };
                }
                
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from('form', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
            },
            
            async submit() {
                this.saving = true;
                const data = {
                    nombre: this.form.nombre,
                    codigo: this.form.codigo,
                    categoria: this.form.categoria,
                    unidad: this.form.unidad,
                    stock: parseInt(this.form.stock) || 0,
                    stockMin: parseInt(this.form.stockMin) || 0,
                    precioCompra: parseFloat(this.form.precioCompra) || 0,
                    precioVenta: parseFloat(this.form.precioVenta) || 0,
                    proveedorId: this.form.proveedorId,
                    ubicacion: this.form.ubicacion
                };
                
                this.$store.inventario.updateProducto(this.producto.id, data);
                this.$store.auditoria.log('editar', 'producto', this.producto.id, data);
                this.$store.toast.success('Producto actualizado');
                window.location.href = `/inventario/${this.producto.id}`;
            }
        }
    }
</script>
@endsection