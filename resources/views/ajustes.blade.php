@extends('layouts.main')

@section('title', 'Ajustes - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="ajustes()" x-show="auth.isAuthenticated && auth.user?.role === 'admin'">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ajustes</h1>
            <p class="text-gray-500 mt-1">Configuración del sistema</p>
        </div>
    </div>
    
    <div x-show="!saving" class="space-y-6">
        <!-- Tarifas -->
        <div class="bg-white rounded-xl border border-gray-100 p-6" x-ref="tarifasCard">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarifas</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pequeño</label>
                    <input type="number" step="0.01" x-model="tarifas.pequeño" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mediano</label>
                    <input type="number" step="0.01" x-model="tarifas.mediano" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grande</label>
                    <input type="number" step="0.01" x-model="tarifas.grande" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recargo por hora</label>
                    <input type="number" step="0.01" x-model="tarifas.recargoHora" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
            </div>
        </div>
        
        <!-- Comisiones -->
        <div class="bg-white rounded-xl border border-gray-100 p-6" x-ref="comisionesCard">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Comisiones</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de cálculo</label>
                    <select x-model="comisiones.tipo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        <option value="porcentaje">Porcentaje</option>
                        <option value="fijo">Valor fijo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor fijo (€)</label>
                    <input type="number" step="0.01" x-model="comisiones.valor" :disabled="comisiones.tipo !== 'fijo'" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Porcentaje por tamaño</label>
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm text-gray-600">Pequeño</span>
                            <input type="number" step="0.01" x-model="comisiones.porTamaño.pequeño" :disabled="comisiones.tipo !== 'porcentaje'" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Mediano</span>
                            <input type="number" step="0.01" x-model="comisiones.porTamaño.mediano" :disabled="comisiones.tipo !== 'porcentaje'" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Grande</span>
                            <input type="number" step="0.01" x-model="comisiones.porTamaño.grande" :disabled="comisiones.tipo !== 'porcentaje'" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Numeración -->
        <div class="bg-white rounded-xl border border-gray-100 p-6" x-ref="numeracionCard">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Numeración</h2>
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prefijo</label>
                    <input type="text" x-model="numeracion.prefijo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serie</label>
                    <input type="text" x-model="numeracion.serie" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siguiente número</label>
                    <input type="number" min="1" step="1" x-model="numeracion.siguiente" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                </div>
            </div>
        </div>
        
        <!-- Métodos de pago -->
        <div class="bg-white rounded-xl border border-gray-100 p-6" x-ref="metodosCard">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Métodos de pago</h2>
            <div class="space-y-3">
                <template x-for="metodo in Object.keys(metodosPago)" :key="metodo">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900 capitalize" x-text="metodo"></p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="metodosPago[metodo]" class="sr-only" />
                                <div class="w-11 h-6 bg-gray-300 rounded-full transition" :class="{ 'bg-brand-blue': metodosPago[metodo] }"></div>
                                <div class="absolute left-1 top-0.5 w-5 h-5 bg-white rounded-full transition" :class="{ 'translate-x-5': metodosPago[metodo] }"></div>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Save button -->
        <div class="flex justify-end mt-6">
            <button @click="saveSettings()" class="btn-primary px-8 py-2.5 rounded-lg font-medium transition" :disabled="saving">
                <span x-show="!saving">Guardar ajustes</span>
                <span x-show="saving" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.562m8.94l-1.898-1.898M12 9v2m0 4v3m2-3v2m-5.5-3A7.953 7.953 0 014 12C4 8.694 5.694 7 8 7m0 0a4.959 4.959 0 011.54 3.033M7 23l-3.5-7m5.618 3.47a1 1 0 01.717.694m14.384.728a1 1 0 01-.717.694M21 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Guardando...
                </span>
            </button>
        </div>
    </div>
    
    <div x-show="!auth.user || auth.user?.role !== 'admin'" class="bg-red-100 border-red-400 rounded-xl p-6 text-red-800">
        <p class="text-sm font-medium">Solo los administradores pueden acceder a esta sección.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function ajustes() {
        return {
            saving: false,
            tarifas: {},
            comisiones: {},
            numeracion: {},
            metodosPago: {},
            
            init() {
                const data = this.$store.settings.data;
                this.tarifas = { ...data.tarifas };
                this.comisiones = JSON.parse(JSON.stringify(data.comisiones));
                this.numeracion = { ...data.numeracion };
                this.metodosPago = { ...data.metodosPago };
                
                this.$nextTick(() => this.animateEntrance());
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                const cards = ['tarifasCard', 'comisionesCard', 'numeracionCard', 'metodosCard'];
                gsap.from(cards.map(id => this.$refs[id]), { opacity: 0, y: 20, duration: 0.4, stagger: 0.1, ease: 'power2.out' });
            },
            
            saveSettings() {
                this.saving = true;
                try {
                    this.$store.settings.data.tarifas = { ...this.tarifas };
                    this.$store.settings.data.comisiones = JSON.parse(JSON.stringify(this.comisiones));
                    this.$store.settings.data.numeracion = { ...this.numeracion };
                    this.$store.settings.data.metodosPago = { ...this.metodosPago };
                    this.$store.settings.save();
                    
                    this.$store.auditoria.log('editar', 'ajustes', null, {
                        tarifas: this.tarifas,
                        comisiones: this.comisiones,
                        numeracion: this.numeracion
                    });
                    
                    this.$store.toast.success('Ajustes guardados exitosamente');
                } catch (err) {
                    console.error('Error guardando ajustes:', err);
                    this.$store.toast.error('Error al guardar los ajustes');
                } finally {
                    this.saving = false;
                }
            }
        }
    }
</script>
@endsection