@extends('layouts.main')

@section('title', 'Editar empleado - Multiservicios Las Acacias')

@section('content')
<div class="max-w-3xl mx-auto" x-data="empleadosEdit()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar empleado</h1>
            <p class="text-gray-500 mt-1">Actualizar datos del personal</p>
        </div>
        <a href="/empleados" class="btn-secondary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver
        </a>
    </div>

    <div x-show="!encontrado" class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-500">
        <p>Empleado no encontrado.</p>
        <a href="/empleados" class="btn-primary inline-flex mt-4 px-4 py-2">Volver a empleados</a>
    </div>

    <form x-show="encontrado" @submit.prevent="saveEmpleado" class="bg-white rounded-xl border border-gray-100 p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo *</label>
                <input type="text" x-model="empleado.nombre" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
                <input type="text" x-model="empleado.telefono" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" x-model="empleado.email" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                <select x-model="empleado.cargo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                    <option value="Lavador">Lavador</option>
                    <option value="Supervisor">Supervisor</option>
                    <option value="Administración">Administración</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Turno</label>
                <select x-model="empleado.turno" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="completo">Completo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de ingreso</label>
                <input type="date" x-model="empleado.fechaIngreso" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select x-model="empleado.activo" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition">
                    <option :value="true">Activo</option>
                    <option :value="false">Inactivo</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
            <textarea x-model="empleado.observaciones" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue transition"></textarea>
        </div>

        <div x-show="error" class="bg-red-100 border border-red-300 rounded-xl p-4 text-red-800">
            <p class="text-sm" x-text="error"></p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="/empleados" class="btn-secondary px-6 py-2.5">Cancelar</a>
            <button type="submit" class="btn-primary px-6 py-2.5" :disabled="saving">
                <span x-show="!saving">Guardar cambios</span>
                <span x-show="saving">Guardando...</span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function empleadosEdit() {
        return {
            empleado: {},
            encontrado: false,
            saving: false,
            error: '',

            init() {
                const id = window.location.pathname.split('/').filter(Boolean).slice(-2)[0];
                const empleado = this.$store.empleados.getById(id);

                if (empleado) {
                    this.empleado = { ...empleado };
                    this.encontrado = true;
                }

                this.$nextTick(() => this.animateEntrance());
            },

            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                gsap.from('form', { opacity: 0, y: 20, duration: 0.3, ease: 'power2.out' });
            },

            saveEmpleado() {
                this.error = '';
                this.saving = true;

                if (!this.empleado.nombre.trim() || !this.empleado.telefono.trim()) {
                    this.error = 'El nombre y el teléfono son obligatorios';
                    this.saving = false;
                    return;
                }

                this.$store.empleados.update(this.empleado.id, {
                    nombre: this.empleado.nombre.trim(),
                    telefono: this.empleado.telefono.trim(),
                    email: (this.empleado.email || '').trim(),
                    cargo: this.empleado.cargo,
                    turno: this.empleado.turno,
                    fechaIngreso: this.empleado.fechaIngreso,
                    activo: this.empleado.activo,
                    observaciones: (this.empleado.observaciones || '').trim()
                });

                this.$store.toast.success('Empleado actualizado');
                setTimeout(() => { window.location.href = '/empleados'; }, 600);
            }
        }
    }
</script>
@endsection
