@extends('layouts.main')

@section('title', 'Nuevo producto - Multiservicios Las Acacias')

@section('content')
<div class="max-w-2xl mx-auto" x-data="inventarioCreate()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo producto</h1>
            <p class="text-gray-500 mt-1">Añadir producto al inventario</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/inventario" class="btn-secondary px-4 py-2">Cancelar</a>
        </div>
    </div>
    
    <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
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
                        <input type="text" x-model="form.codigo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Auto-generado si vacío">
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
            
            <!-- Stock -->
            <div class="border-t border-gray-100 pt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock y precios</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock inicial</label>
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
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="/inventario" class="btn-secondary px-6 py-2.5">Cancelar</a>
            <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving">Crear producto</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function inventarioCreate() {
        return {
            form: {
                nombre: '',
                codigo: '',
                categoria: '',
                unidad: 'ud',
                stock: 0,
                stockMin: 5,
                precioCompra: 0,
                precioVenta: 0,
                proveedorId: '',
                ubicacion: ''
            },
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
                if (!this.form.codigo) {
                    this.form.codigo = 'INV-' + Date.now().toString(36).toUpperCase();
                }
            },
            
            async submit() {
                this.saving = true;
                const producto = this.$store.inventario.createProducto(this.form);
                this.$store.toast.success(`Producto ${producto.nombre} creado`);
                window.location.href = `/inventario/${producto.id}`;
            }
        }
    }
</script>
@endsection