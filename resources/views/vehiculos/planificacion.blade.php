@extends('layouts.main')

@section('title', 'Planificación de vehículos - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="vehiculosPlanificacion()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="/vehiculos" class="inline-flex items-center gap-2 text-sm text-brand-blue hover:text-brand-blue-dark font-medium mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a vehículos
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Planificación de vehículos</h1>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">Demostración</span>
            </div>
            <p class="text-gray-500 mt-1">Orden propuesto para atender los vehículos en espera</p>
        </div>
        <button @click="generar()" class="btn-primary px-4 py-2.5 flex items-center gap-2" :disabled="generando || vehiculos.length === 0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span x-text="generando ? 'Calculando...' : 'Generar propuesta'"></span>
        </button>
    </div>

    <!-- Aviso -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-brand-blue flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="text-sm text-blue-900">
            <strong>Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.</strong>
            La función de aptitud definitiva está pendiente; esta pantalla usa una propuesta de demostración basada en antigüedad en la cola y tamaño del vehículo.
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
            <p class="text-3xl font-bold text-amber-600" x-text="esperaPromedio + ' min'"></p>
            <p class="text-sm text-gray-500">Espera promedio</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900" x-text="resultado ? resultado.generaciones : 0"></p>
            <p class="text-sm text-gray-500">Generaciones</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Orden propuesto -->
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Orden de atención propuesto</h2>
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
                            <th class="px-4 py-3">Entrada</th>
                            <th class="px-4 py-3">Espera</th>
                            <th class="px-4 py-3 text-right">Prioridad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="v in orden" :key="v.id">
                            <tr class="hover:bg-gray-50 transition-colors" :class="v.posicion <= 3 ? 'bg-blue-50/40' : ''">
                                <td class="px-4 py-4">
                                    <span class="inline-flex w-7 h-7 items-center justify-center rounded-full text-sm font-semibold" :class="v.posicion <= 3 ? 'bg-brand-blue text-white' : 'bg-gray-100 text-gray-600'" x-text="v.posicion"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-mono text-sm font-medium text-gray-900" x-text="v.placa"></p>
                                    <p class="text-xs text-gray-500" x-text="v.clienteNombre"></p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="v.tamano === 'pequeño' ? 'bg-blue-100 text-blue-800' : v.tamano === 'mediano' ? 'bg-cyan-100 text-cyan-800' : 'bg-indigo-100 text-indigo-800'" x-text="v.tamano"></span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(v.entrada?.fechaEntrada)"></td>
                                <td class="px-4 py-4 text-sm font-medium text-amber-600" x-text="esperaTexto(v.esperaMin)"></td>
                                <td class="px-4 py-4 text-right text-sm font-medium text-gray-900" x-text="v.prioridad"></td>
                            </tr>
                        </template>
                        <tr x-show="orden.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <p>No hay vehículos dentro del local para planificar</p>
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

        <!-- Trabajadores disponibles -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Trabajadores disponibles</h2>
                <p class="text-sm text-gray-500">Personal activo para asignar</p>
            </div>
            <div class="p-4 space-y-3">
                <template x-for="t in trabajadores" :key="t.id">
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                        <div class="w-9 h-9 rounded-full bg-brand-blue-soft flex items-center justify-center text-brand-blue font-semibold text-sm" x-text="iniciales(t.nombre)"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="t.nombre"></p>
                            <p class="text-xs text-gray-500" x-text="t.cargo + ' · ' + t.turno"></p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Disponible</span>
                    </div>
                </template>
                <p x-show="trabajadores.length === 0" class="text-sm text-gray-500 text-center py-4">No hay trabajadores activos registrados</p>
            </div>
            <div class="p-4 border-t border-gray-100">
                <a href="/lavados/planificacion" class="btn-secondary w-full text-center px-4 py-2.5 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Asignar trabajadores
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function vehiculosPlanificacion() {
        return {
            vehiculos: [],
            trabajadores: [],
            orden: [],
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
                if (this.vehiculos.length === 0) {
                    this.orden = [];
                    this.resultado = null;
                    return;
                }

                this.generando = true;

                // Pequeña pausa para que se perciba el cálculo sin bloquear la interfaz.
                setTimeout(() => {
                    this.resultado = window.Metaheuristica.ordenarVehiculos(this.vehiculos, this.opciones);
                    this.orden = this.resultado.orden;
                    this.generando = false;
                }, 120);
            },

            get esperaPromedio() {
                if (!this.vehiculos.length) return 0;
                const total = this.vehiculos.reduce((suma, v) => {
                    const entrada = v.entrada?.fechaEntrada;
                    return suma + (entrada ? (Date.now() - new Date(entrada).getTime()) / 60000 : 0);
                }, 0);
                return Math.round(total / this.vehiculos.length);
            },

            esperaTexto(minutos) {
                const min = Math.max(0, Math.round(minutos || 0));
                if (min < 60) return min + ' min';
                const horas = Math.floor(min / 60);
                return horas + 'h ' + (min % 60) + 'm';
            },

            iniciales(nombre) {
                return nombre.split(' ').map(p => p.charAt(0)).slice(0, 2).join('').toUpperCase();
            }
        }
    }
</script>
@endsection
