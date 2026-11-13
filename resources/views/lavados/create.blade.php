@extends('layouts.main')

@section('title', 'Registrar lavado - Multiservicios Las Acacias')

@section('content')
<div class="max-w-xl mx-auto" x-data="lavadosCreate()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrar lavado</h1>
            <p class="text-gray-500 mt-1">Registro de lavado y comisión de empleado</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/lavados" class="btn-secondary px-4 py-2">Cancelar</a>
        </div>
    </div>
    
    <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vehículo</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                        <div class="relative">
                            <input type="text" x-model="placa" x-on:blur="buscarVehiculo" placeholder="Ej: 1234ABC" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue transition" style="text-transform: uppercase;">
                            <ul x-show="vehiculosSugeridos.length > 0 && placa" class="absolute left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-50 max-h-48 overflow-y-auto" style="z-index: 50;">
                                <template x-for="v in vehiculosSugeridos" :key="v.placa">
                                    <li @click="seleccionarVehiculo(v)" class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b last:border-0">
                                        <span class="font-mono text-sm font-medium text-gray-900" x-text="v.placa"></span>
                                        <span class="text-xs text-gray-500" x-text="v.clienteNombre"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <div x-show="selectedVehiculo">
                        <p class="text-sm text-gray-500 font-medium">Cliente:</p>
                        <span class="font-medium text-gray-900" x-text="selectedVehiculo?.clienteNombre || ''"></span>
                        <p class="text-sm text-gray-500 font-medium">Tamaño actual:</p>
                        <span class="font-medium text-gray-900 capitalize" x-text="selectedVehiculo?.tamano || ''"></span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empleado *</label>
                    <select x-model="form.empleadoId" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option value="">Seleccionar empleado</option>
                        <template x-for="emp in empleados" :key="emp.id">
                            <option :value="emp.id" x-text="emp.nombre"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tamaño *</label>
                    <select x-model="form.tamano" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option value="pequeño">Pequeño</option>
                        <option value="mediano">Mediano</option>
                        <option value="grande">Grande</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio base *</label>
                    <input type="number" step="0.01" x-model="form.precio" @input="comisionEstimada = calcularComision(form.tamano, form.precio)" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comisión estimada</label>
                    <input type="number" step="0.01" x-model="comisionEstimada" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-900 text-amber-700 font-medium focus:outline-none">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea x-model="form.observaciones" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue transition" placeholder="Notas adicionales..."></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="/lavados" class="btn-secondary px-6 py-2.5">Cancelar</a>
                <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving || !canSubmit()">
                    <span x-show="!saving">Registrar lavado</span>
                    <span x-show="saving" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-6.219-8.56"></path></svg>
                        Registrando...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function lavadosCreate() {
        return {
            placa: '',
            selectedVehiculo: null,
            vehiculosSugeridos: [],
            form: {
                vehiculoId: '',
                vehiculoPlaca: '',
                clienteNombre: '',
                empleadoId: '',
                tamano: 'pequeño',
                precio: 0,
                comision: 0,
                observaciones: ''
            },
            empleados: [],
            comisionEstimada: 0,
            saving: false,
            
            init() {
                this.empleados = this.$store.empleados.getActivos();
                this.comisionEstimada = this.calcularComision(this.form.tamano, this.form.precio);
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from('form', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
            },
            
            calcularComision(tamano, precio) {
                const pct = this.$store.settings.getComision(tamano);
                return Math.round(precio * pct * 100) / 100;
            },
            
            
            buscarVehiculo() {
                const q = this.placa.toUpperCase().trim();
                if (!q) {
                    this.vehiculosSugeridos = [];
                    return;
                }
                this.vehiculosSugeridos = this.$store.vehiculos.getAll()
                    .filter(v => v.placa.toUpperCase().includes(q))
                    .slice(0, 10);
            },
            
            seleccionarVehiculo(v) {
                this.placa = v.placa;
                this.selectedVehiculo = v;
                this.vehiculosSugeridos = [];
                this.form.tamano = v.tamano;
                this.comisionEstimada = this.calcularComision(v.tamano, this.form.precio);
            },
            
            canSubmit() {
                return !!this.selectedVehiculo && this.form.precio > 0 && this.form.empleadoId;
            },
            
            async submit() {
                this.saving = true;
                
                this.form.vehiculoId = this.selectedVehiculo.id;
                this.form.vehiculoPlaca = this.selectedVehiculo.placa;
                this.form.clienteNombre = this.selectedVehiculo.clienteNombre;
                this.form.comision = this.comisionEstimada;
                
                this.$store.lavados.create({
                    vehiculoId: this.form.vehiculoId,
                    vehiculoPlaca: this.form.vehiculoPlaca,
                    vehiculoTamano: this.form.tamano,
                    clienteNombre: this.form.clienteNombre,
                    empleadoId: parseInt(this.form.empleadoId),
                    empleadoNombre: this.$store.empleados.getById(this.form.empleadoId)?.nombre || '',
                    precio: parseFloat(this.form.precio),
                    comision: this.form.comision,
                    estado: 'completado',
                    observaciones: this.form.observaciones
                });
                
                this.$store.toast.success('Lavado registrado exitosamente');
                setTimeout(() => { window.location.href = '/lavados'; }, 600);
            }
        }
    }
</script>
@endsection