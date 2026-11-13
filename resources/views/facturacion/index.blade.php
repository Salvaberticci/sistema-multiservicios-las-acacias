@extends('layouts.main')

@section('title', 'Facturación - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="facturacionIndex()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Facturación</h1>
            <p class="text-gray-500 mt-1">Gestión de facturas y cobros</p>
        </div>
        <a href="/facturacion/create" class="btn-primary px-4 py-2.5 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nueva factura
        </a>
    </div>
    
    <!-- Toolbar -->
    <div class="bg-white rounded-xl border border-gray-100 p-4" x-ref="toolbar">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" x-model="search" @input="filter()" placeholder="Buscar por número, cliente..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition">
            </div>
            
            <!-- Filters -->
            <div class="flex items-center gap-3">
                <select x-model="estadoFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todos los estados</option>
                    <option value="borrador">Borrador</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                    <option value="anulada">Anulada</option>
                </select>
                
                <select x-model="fechaFilter" @change="filter()" class="px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-blue">
                    <option value="">Todo el tiempo</option>
                    <option value="hoy">Hoy</option>
                    <option value="semana">Esta semana</option>
                    <option value="mes">Este mes</option>
                </select>
                
                <button @click="exportCSV()" class="btn-secondary px-4 py-2.5 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exportar CSV
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden" x-ref="tableContainer">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">Número</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Método pago</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="(factura, index) in paginatedFacturas" :key="factura.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <span class="font-mono text-sm font-medium text-gray-900" x-text="factura.numero"></span>
                            </td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900" x-text="factura.clienteNombre"></p>
                                    <p class="text-xs text-gray-500" x-text="factura.clienteTelefono"></p>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600" x-text="utils.formatDate(factura.fecha)"></td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-semibold text-gray-900" x-text="utils.formatCurrency(factura.total)"></span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full" :class="estadoClass(factura.estado)" x-text="estadoLabel(factura.estado)"></span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 capitalize" x-text="factura.metodoPago || '-'"></td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a :href="`/facturacion/${factura.id}`" class="p-2 text-gray-500 hover:text-brand-blue hover:bg-brand-blue-soft rounded-lg transition" title="Ver detalle">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a :href="`/facturacion/${factura.id}/edit`" x-show="factura.estado === 'borrador'" class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="anular(factura)" x-show="factura.estado !== 'anulada'" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Anular">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredFacturas.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p>No se encontraron facturas</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between" x-show="filteredFacturas.length > 0">
            <p class="text-sm text-gray-500">Mostrando <span x-text="paginatedStart"></span> a <span x-text="paginatedEnd"></span> de <span x-text="filteredFacturas.length"></span> facturas</p>
            <div class="flex gap-2">
                <button @click="prevPage()" :disabled="page === 1" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <template x-for="p in visiblePages" :key="p">
                    <button @click="goPage(p)" :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition', page === p ? 'bg-brand-blue text-white' : 'text-gray-600 hover:bg-gray-100']" x-text="p"></button>
                </template>
                <button @click="nextPage()" :disabled="page === totalPages" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function facturacionIndex() {
        return {
            search: '',
            estadoFilter: '',
            fechaFilter: '',
            page: 1,
            perPage: 10,
            facturas: [],
            filteredFacturas: [],
            
            init() {
                this.facturas = this.$store.facturas.getAll();
                this.filter();
                
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from(this.$refs.toolbar, { opacity: 0, y: -10, duration: 0.3, ease: 'power2.out' });
                gsap.from(this.$refs.tableContainer, { opacity: 0, y: 10, duration: 0.3, ease: 'power2.out', delay: 0.1 });
                
                // Stagger rows
                setTimeout(() => {
                    const rows = this.$refs.tableContainer.querySelectorAll('tbody tr');
                    gsap.from(rows, { opacity: 0, y: 12, duration: 0.3, stagger: 0.04, ease: 'power2.out' });
                }, 100);
            },
            
            filter() {
                let result = [...this.facturas];
                
                if (this.search) {
                    const q = this.search.toLowerCase();
                    result = result.filter(f => 
                        f.numero.toLowerCase().includes(q) ||
                        f.clienteNombre.toLowerCase().includes(q)
                    );
                }
                
                if (this.estadoFilter) {
                    result = result.filter(f => f.estado === this.estadoFilter);
                }
                
                if (this.fechaFilter) {
                    const ahora = new Date();
                    result = result.filter(f => {
                        const fecha = new Date(f.fecha);
                        if (this.fechaFilter === 'hoy') return fecha.toDateString() === ahora.toDateString();
                        if (this.fechaFilter === 'semana') {
                            const semanaAtras = new Date(ahora.getTime() - 7 * 24 * 60 * 60 * 1000);
                            return fecha >= semanaAtras;
                        }
                        if (this.fechaFilter === 'mes') {
                            const mesAtras = new Date(ahora.getFullYear(), ahora.getMonth() - 1, ahora.getDate());
                            return fecha >= mesAtras;
                        }
                        return true;
                    });
                }
                
                this.filteredFacturas = result;
                this.page = 1;
            },
            
            get paginatedFacturas() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredFacturas.slice(start, start + this.perPage);
            },
            
            get paginatedStart() {
                return this.filteredFacturas.length > 0 ? (this.page - 1) * this.perPage + 1 : 0;
            },
            
            get paginatedEnd() {
                return Math.min(this.page * this.perPage, this.filteredFacturas.length);
            },
            
            get totalPages() {
                return Math.ceil(this.filteredFacturas.length / this.perPage) || 1;
            },
            
            get visiblePages() {
                const pages = [];
                const total = this.totalPages;
                const current = this.page;
                
                for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
                    pages.push(i);
                }
                return pages;
            },
            
            prevPage() { if (this.page > 1) this.page--; },
            nextPage() { if (this.page < this.totalPages) this.page++; },
            goPage(p) { this.page = p; },
            
            estadoClass(estado) {
                const classes = {
                    borrador: 'bg-gray-100 text-gray-800',
                    pendiente: 'bg-amber-100 text-amber-800',
                    pagada: 'bg-green-100 text-green-800',
                    anulada: 'bg-red-100 text-red-800'
                };
                return classes[estado] || classes.borrador;
            },
            
            estadoLabel(estado) {
                const labels = {
                    borrador: 'Borrador',
                    pendiente: 'Pendiente',
                    pagada: 'Pagada',
                    anulada: 'Anulada'
                };
                return labels[estado] || estado;
            },
            
            anular(factura) {
                this.$store.modal.open({
                    title: 'Anular factura',
                    message: `¿Está seguro de anular la factura ${factura.numero}?`,
                    variant: 'danger',
                    onConfirm: () => {
                        this.$store.facturas.anular(factura.id);
                        this.$store.toast.success('Factura anulada');
                        this.facturas = this.$store.facturas.getAll();
                        this.filter();
                    }
                });
            },
            
            exportCSV() {
                const headers = ['Número', 'Cliente', 'Fecha', 'Total', 'Estado', 'Método pago'];
                const rows = this.filteredFacturas.map(f => [
                    f.numero, f.clienteNombre, this.$store.utils.formatDate(f.fecha),
                    this.$store.utils.formatCurrency(f.total), this.estadoLabel(f.estado), f.metodoPago || ''
                ]);
                const csv = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(',')).join('\n');
                
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `facturas_${new Date().toISOString().split('T')[0]}.csv`;
                link.click();
                
                this.$store.toast.success('CSV exportado');
            }
        }
    }
</script>
@endsection