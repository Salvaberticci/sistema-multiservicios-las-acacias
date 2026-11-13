@extends('layouts.main')

@section('title', 'Editar lavado - Multiservicios Las Acacias')

@section('content')
<div class="max-w-xl mx-auto" x-data="lavadosEdit()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar lavado</h1>
            <p class="text-gray-500 mt-1">Modificar datos del lavado</p>
        </div>
        <div class="flex items-center gap-3">
            <a :href="`/lavados/${lavado?.id}`" class="btn-secondary px-4 py-2">Volver</a>
        </div>
    </div>
    
    <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Datos del lavado</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehículo</label>
                        <input type="text" x-model="form.vehiculoPlaca" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño</label>
                            <select x-model="form.tamano" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="pequeño">Pequeño</option>
                                <option value="mediano">Mediano</option>
                                <option value="grande">Grande</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <input type="text" x-model="form.clienteNombre" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empleado</label>
                            <input type="text" x-model="form.empleadoNombre" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                            <input type="number" step="0.01" x-model.number="form.precio" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comisión</label>
                            <input type="number" step="0.01" x-model.number="form.comision" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select x-model="form.estado" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                                <option value="completado">Completado</option>
                                <option value="anulado">Anulado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea x-model="form.observaciones" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue transition" placeholder="Notas adicionales..."></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a :href="`/lavados/${lavado.id}`" class="btn-secondary px-6 py-2.5">Cancelar</a>
                <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>

@endsection
@section('scripts')
<script>
    function lavadosEdit() {
        return {
            lavado: {},
            form: {},
            saving: false,
            
            init() {
                const id = window.location.pathname.split('/').pop();
                this.lavado = this.$store.lavados.getById(id) || {};
                
                if (this.lavado) {
                    this.form = {
                        vehiculoPlaca: this.lavado.vehiculoPlaca,
                        tamano: this.lavado.vehiculoTamano,
                        clienteNombre: this.lavado.clienteNombre,
                        empleadoNombre: this.lavado.empleadoNombre,
                        precio: this.lavado.precio,
                        comision: this.lavado.comision,
                        estado: this.lavado.estado,
                        observaciones: this.lavado.observaciones
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
                this.$store.lavados.update(this.lavado.id, this.form);
                this.$store.auditoria.log('editar', 'lavado', this.lavado.id, this.form);
                this.$store.toast.success('Lavado actualizado');
                setTimeout(() => { window.location.href = `/lavados/${this.lavado.id}`; }, 600);
            }
        }
    }
</script>
@endsection