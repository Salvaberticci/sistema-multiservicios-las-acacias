@extends('layouts.main')

@section('title', 'Registrar entrada - Multiservicios Las Acacias')

@section('content')
<div class="max-w-2xl mx-auto" x-data="vehiculoEntrada()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registrar entrada</h1>
            <p class="text-gray-500 mt-1">Nuevo vehículo en el establecimiento</p>
        </div>
        <a href="/vehiculos" class="btn-secondary px-4 py-2">Volver</a>
    </div>
    
    <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                <input type="text" x-model="form.placa" @input="form.placa = form.placa.toUpperCase()" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 uppercase focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="1234ABC" autocomplete="off" autofocus>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <div class="relative">
                    <select x-model="form.clienteId" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue appearance-none">
                        <option value="">Buscar o seleccionar cliente...</option>
                        <template x-for="cliente in clientes" :key="cliente.id">
                            <option :value="cliente.id" x-text="cliente.nombre + ' - ' + cliente.telefono"></option>
                        </template>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tamaño *</label>
                <div class="grid grid-cols-3 gap-3">
                    <template x-for="t in tamanos" :key="t.key">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="tamano" :value="t.key" x-model="form.tamano" class="sr-only">
                            <div class="p-4 border-2 rounded-xl text-center transition-all" :class="form.tamano === t.key ? 'border-brand-blue bg-brand-blue-soft' : 'border-gray-200 hover:border-gray-300'">
                                <svg :class="['w-8 h-8 mx-auto mb-2', form.tamano === t.key ? 'text-brand-blue' : 'text-gray-400']" x-html="t.icon"></svg>
                                <p class="font-medium" :class="form.tamano === t.key ? 'text-brand-blue' : 'text-gray-700'" x-text="t.label"></p>
                            </div>
                        </label>
                    </template>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea x-model="form.observaciones" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Opcional"></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="/vehiculos" class="btn-secondary px-6 py-2.5">Cancelar</a>
            <button type="submit" class="btn-primary px-6 py-2.5" :disabled="submitting">
                <span x-show="!submitting">Registrar entrada</span>
                <span x-show="submitting" class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Registrando...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function vehiculoEntrada() {
        return {
            form: { placa: '', clienteId: '', tamano: '', observaciones: '' },
            clientes: [],
            tamanos: [
                { key: 'pequeño', label: 'Pequeño', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 19l7-7 3 3-7 7-3-3z"/>' },
                { key: 'mediano', label: 'Mediano', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 19l7-7 3 3-7 7-3-3z"/>' },
                { key: 'grande', label: 'Grande', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 19l7-7 3 3-7 7-3-3z"/>' }
            ],
            submitting: false,
            
            init() {
                this.clientes = this.$store.clientes.getAll();
                // Default to mediano
                this.form.tamano = 'mediano';
            },
            
            async submit() {
                if (!this.form.placa || !this.form.clienteId || !this.form.tamano) {
                    this.$store.toast.error('Complete todos los campos obligatorios');
                    return;
                }
                
                this.submitting = true;
                this.$store.vehiculos.registrarEntrada(this.form);
                this.$store.toast.success(`Entrada registrada para ${this.form.placa}`);
                window.location.href = '/vehiculos';
            }
        }
    }
</script>
@endsection