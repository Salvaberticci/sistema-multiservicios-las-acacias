@extends('layouts.main')

@section('title', 'Asignación de trabajadores - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="lavadosPlanificacion()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="/lavados" class="inline-flex items-center gap-2 text-sm text-brand-blue hover:text-brand-blue-dark font-medium mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a lavados
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Asignación de trabajadores</h1>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Demostración</span>
            </div>
            <p class="text-gray-500 mt-1">Propuesta de asignación de vehículos en espera a trabajadores disponibles</p>
        </div>
        <button @click="generar()" class="btn-primary px-4 py-2.5 flex items-center gap-2" :disabled="generando || vehiculos.length === 0 || trabajadores.length === 0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span x-text="generando ? 'Calculando...' : 'Generar asignación'"></span>
        </button>
    </div>

    <!-- Aviso -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-brand-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-sm text-blue-900">
            <strong>Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.</strong>
            La función de aptitud definitiva está pendiente; esta pantalla usa una propuesta de demostración que equilibra la carga por tamaño del vehículo.
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-ref="statsGrid">
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-brand-blue" x-text="vehiculos.length"></p>
            <p class="text-sm text-gray-500">Vehículos en espera</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-green-600" x-text="trabajadores.length"></p>
            <p class="text-sm text-gray-500">Trabajadores disponibles</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-amber-600" x-text="resultado ? resultado.asignaciones.length : 0"></p>
            <p class="text-sm text-gray-500">Vehículos asignados</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900" x-text="cargaMaxima"></p>
            <p class="text-sm text-gray-500">Carga máxima</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Asignaciones -->
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Asignación propuesta</h2>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <label class="text-gray-500">Población</label>
                    <select x-model.number="opciones.poblacion" class="px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option :value="20">20</option>
                        <option :value="40">40</option>
                        <option :value="60">60</option>
                    </select>
                    <label class="text-gray-500">Generaciones</label>
                    <select x-model.number="opciones.generaciones" class="px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue">
                        <option :value="30">30</option>
                        <option :value="60">60</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Vehículo</th>
                            <th class="px-4 py-3">Tamaño</th>
                            <th class="px-4 py-3">Trabajador asignado</th>
                            <th class="px-4 py-3 text-right">Carga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(a, index) in asignaciones" :key="a.vehiculo.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="inline-flex w-7 h-7 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600" x-text="index + 1"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-mono text-sm font-medium text-gray-900" x-text="a.vehiculo.placa"></p>
                                    <p class="text-xs text-gray-500" x-text="a.vehiculo.clienteNombre"></p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="a.vehiculo.tamano === 'pequeño' ? 'bg-blue-100 text-blue-800' : a.vehiculo.tamano === 'mediano' ? 'bg-cyan-100 text-cyan-800' : 'bg-indigo-100 text-indigo-800'" x-text="a.vehiculo.tamano"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-semibold text-xs" x-text="iniciales(a.trabajador.nombre)"></div>
                                        <span class="text-sm font-medium text-gray-900" x-text="a.trabajador.nombre"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right text-sm font-medium text-gray-900" x-text="a.carga"></td>
                            </tr>
                        </template>
                        <tr x-show="asignaciones.length === 0">
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                <p>No hay vehículos o trabajadores disponibles para asignar</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-wrap items-center justify-between gap-2 text-sm">
                <span class="text-gray-500">Aptitud de la mejor propuesta</span>
                <span class="font-semibold text-gray-900" x-text="resultado ? resultado.fitness : '-'"></span>
            </div>
        </div>

        <!-- Carga por trabajador -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Carga por trabajador</h2>
                <p class="text-sm text-gray-500">Vehículos y peso asignado</p>
            </div>
            <div class="p-4 space-y-3">
                <template x-for="c in cargas" :key="c.trabajador.id">
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-semibold text-xs" x-text="iniciales(c.trabajador.nombre)"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="c.trabajador.nombre"></p>
                                    <p class="text-xs text-gray-500" x-text="c.trabajador.turno"></p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900" x-text="c.carga"></span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-brand-blue to-brand-blue-light rounded-full transition-all" :style="`width: ${porcentajeCarga(c.carga)}%`"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" x-text="c.vehiculos + ' vehículo(s)'"></p>
                    </div>
                </template>
                <p x-show="cargas.length === 0" class="text-sm text-gray-500 text-center py-4">Sin trabajadores disponibles</p>
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="/vehiculos/planificacion" class="btn-secondary w-full text-center px-4 py-2.5 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17h14l1.2-4.4a3 3 0 00-1.4-3.4l-2.1-1.1-1.5-2.2A3 3 0 0012.7 5H9.5a3 3 0 00-2.4 1.2L5.6 8.1 3.8 9a3 3 0 00-1.6 3.3L3.4 17H5z"></path><circle cx="7.5" cy="17.5" r="1.8"></circle><circle cx="16.5" cy="17.5" r="1.8"></circle></svg>
                    Ver orden de vehículos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function lavadosPlanificacion() {
        return {
            vehiculos: [],
            trabajadores: [],
            resultado: null,
            generando: false,
            opciones: { poblacion: 40, generaciones: 60, mutacion: 0.2, semilla: 42 },

            init() {
                this.vehiculos = this.$store.vehiculos.getDentro();
                this.trabajadores = this.$store.empleados.getActivos();
                this.generar();
                this.$nextTick(() => this.animateEntrance());
            },

            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from(this.$refs.statsGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
            },

            generar() {
                if (!this.vehiculos.length || !this.trabajadores.length) {
                    this.resultado = null;
                    return;
                }

                this.generando = true;

                setTimeout(() => {
                    this.resultado = window.Metaheuristica.asignarTrabajadores(this.vehiculos, this.trabajadores, this.opciones);
                    this.generando = false;
                }, 120);
            },

            get asignaciones() {
                return this.resultado?.asignaciones || [];
            },

            get cargas() {
                return this.resultado?.cargas || [];
            },

            get cargaMaxima() {
                if (!this.resultado?.cargas?.length) return 0;
                return Math.max(...this.resultado.cargas.map(c => c.carga));
            },

            porcentajeCarga(carga) {
                if (!this.cargaMaxima) return 0;
                return Math.round((carga / this.cargaMaxima) * 100);
            },

            iniciales(nombre) {
                return nombre.split(' ').map(p => p.charAt(0)).slice(0, 2).join('').toUpperCase();
            }
        }
    }
</script>
@endsection
