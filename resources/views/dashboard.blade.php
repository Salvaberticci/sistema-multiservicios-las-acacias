@extends('layouts.main')

@section('title', 'Dashboard - Multiservicios Las Acacias')

@section('content')
<div class="space-y-6" x-data="dashboard()">
    <!-- Hero -->
    <section class="surface-hero p-6 md:p-8">
        <svg class="car-watermark hidden md:block" viewBox="0 0 220 80" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 58h12l6-15h24l14-10c5-3.5 10-4 16-4h20c9 0 16 3 23 9l10 9 18 4c9 2 14 4 14 7"></path>
            <path d="M54 43h30l8-12"></path>
            <path d="M96 31h26c7 0 12 2 17 7l6 5"></path>
            <circle cx="60" cy="58" r="11"></circle>
            <circle cx="172" cy="58" r="11"></circle>
        </svg>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="max-w-xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip chip-dark"><span class="status-dot"></span> En vivo</span>
                    <span class="chip chip-blue" x-text="$store.settings.data.general.nombreNegocio"></span>
                </div>
                <h1 class="mt-4 text-2xl font-bold tracking-tight text-white md:text-[2rem]">
                    Hola, <span x-text="$store.auth.user?.name?.split(' ')[0] || 'equipo'"></span>.
                    <span class="block text-gradient text-xl md:text-2xl">Todo tu taller, bajo control.</span>
                </h1>
                <p class="mt-3 text-sm text-white/55">
                    <span class="first-letter:uppercase" x-text="todayLabel"></span> · Facturación, lavados, vehículos y stock en tiempo real.
                </p>
                <div class="mt-5 flex flex-wrap gap-2.5">
                    <a href="/facturacion/create" class="btn btn-primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nueva factura
                    </a>
                    <button @click="$store.commandPalette.toggle()" class="btn btn-secondary bg-white/6 text-white border-white/12 hover:bg-white/10 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Buscar
                        <kbd class="ml-1 rounded border border-white/15 bg-white/8 px-1.5 py-0.5 text-[0.6rem] text-white/60">⌘K</kbd>
                    </button>
                </div>
            </div>

            <div class="relative z-10 flex items-center gap-5 rounded-2xl border border-white/10 bg-white/4 px-5 py-4 backdrop-blur-sm">
                <div class="gauge-ring">
                    <span x-text="Math.min(100, Math.round((stats.lavadosHoy / 20) * 100)) + '%'"></span>
                </div>
                <div>
                    <p class="text-[0.62rem] font-semibold uppercase tracking-[0.2em] text-white/45">Meta diaria</p>
                    <p class="mt-1 text-sm font-semibold text-white">
                        <span x-text="stats.lavadosHoy"></span> / 20 lavados
                    </p>
                    <p class="mt-1 text-[0.7rem] text-white/40">Ritmo de operación</p>
                </div>
            </div>
        </div>
    </section>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" x-ref="kpiGrid">
        <kpi-card :value="stats.facturasHoy" label="Facturas hoy" icon="receipt" format="number"></kpi-card>
        <kpi-card :value="stats.vehiculosDentro" label="Vehículos dentro" icon="car" format="number"></kpi-card>
        <kpi-card :value="stats.lavadosHoy" label="Lavados hoy" icon="droplet" format="number"></kpi-card>
        <kpi-card :value="stats.stockBajo" label="Stock bajo" icon="alert" format="number"></kpi-card>
    </div>

    <!-- Chart + lateral -->
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="surface p-5 md:p-6 xl:col-span-2" x-ref="chartCard">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Lavados últimos <span x-text="chartPeriod"></span> días</h2>
                    <p class="mt-1 text-xs text-steel-500">Volumen de servicios registrados por día</p>
                </div>
                <select x-model="chartPeriod" class="rounded-lg border border-mist bg-white px-3 py-1.5 text-sm text-steel-700 focus:border-brand-blue-light focus:outline-none">
                    <option value="7">7 días</option>
                    <option value="30">30 días</option>
                    <option value="90">90 días</option>
                </select>
            </div>
            <div class="mt-5 rounded-xl border border-mist/70 bg-gradient-to-b from-white to-brand-blue-soft/20 p-3">
                <canvas class="h-64 w-full" x-ref="chartCanvas"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-mist bg-white/70 px-3 py-2">
                    <p class="text-[0.6rem] uppercase tracking-[0.14em] text-steel-500">Ingresos hoy</p>
                    <p class="mt-0.5 text-sm font-semibold text-steel-900" x-text="utils.formatCurrency(stats.ingresoHoy)"></p>
                </div>
                <div class="rounded-xl border border-mist bg-white/70 px-3 py-2">
                    <p class="text-[0.6rem] uppercase tracking-[0.14em] text-steel-500">Comisiones hoy</p>
                    <p class="mt-0.5 text-sm font-semibold text-brand-blue" x-text="utils.formatCurrency(stats.comisionHoy)"></p>
                </div>
                <div class="rounded-xl border border-mist bg-white/70 px-3 py-2">
                    <p class="text-[0.6rem] uppercase tracking-[0.14em] text-steel-500">Pendientes cobro</p>
                    <p class="mt-0.5 text-sm font-semibold text-brand-red" x-text="stats.pendientes"></p>
                </div>
                <div class="rounded-xl border border-mist bg-white/70 px-3 py-2">
                    <p class="text-[0.6rem] uppercase tracking-[0.14em] text-steel-500">Lavados activos</p>
                    <p class="mt-0.5 text-sm font-semibold text-steel-900" x-text="stats.lavadosHoy"></p>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <!-- Accesos rápidos -->
            <div class="surface p-5 md:p-6" x-ref="quickActions">
                <div class="panel-head">
                    <h2 class="panel-title">Accesos rápidos</h2>
                    <span class="chip chip-blue">Atajos</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <a href="/facturacion/create" class="group rounded-xl border border-brand-blue/12 bg-gradient-to-br from-brand-blue-soft/70 to-white p-4 transition hover:-translate-y-0.5 hover:border-brand-blue-light/40 hover:shadow-glow-blue">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-blue text-white shadow-glow-blue">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </span>
                        <p class="mt-3 text-xs font-semibold leading-tight text-steel-900">Nueva factura</p>
                    </a>
                    <a href="/vehiculos/entrada" class="group rounded-xl border border-emerald-200/70 bg-gradient-to-br from-emerald-50 to-white p-4 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-[0_18px_50px_-22px_rgba(16,185,129,0.5)]">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-emerald-600 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-6 6m6-6l6 6"></path></svg>
                        </span>
                        <p class="mt-3 text-xs font-semibold leading-tight text-steel-900">Entrada vehículo</p>
                    </a>
                    <a href="/lavados/create" class="group rounded-xl border border-brand-blue-light/25 bg-gradient-to-br from-brand-blue-soft/60 to-white p-4 transition hover:-translate-y-0.5 hover:border-brand-blue-light/50 hover:shadow-glow-blue">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-blue-light text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1m0-12.8l-2.1 2.1m-8.6 8.6l-2.1 2.1"></path><circle cx="12" cy="12" r="3.5"></circle></svg>
                        </span>
                        <p class="mt-3 text-xs font-semibold leading-tight text-steel-900">Registrar lavado</p>
                    </a>
                    <a href="/inventario/create" class="group rounded-xl border border-amber-200/70 bg-gradient-to-br from-amber-50 to-white p-4 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-[0_18px_50px_-22px_rgba(245,158,11,0.5)]">
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-amber-500 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </span>
                        <p class="mt-3 text-xs font-semibold leading-tight text-steel-900">Nuevo producto</p>
                    </a>
                </div>
            </div>

            <!-- Próximas salidas -->
            <div class="surface p-5 md:p-6" x-ref="proximasSalidas">
                <div class="panel-head">
                    <h2 class="panel-title">Vehículos dentro</h2>
                    <span class="chip chip-green" x-text="proximasSalidas.length + ' activos'"></span>
                </div>
                <div x-show="proximasSalidas.length === 0" class="py-8 text-center text-sm text-steel-500">
                    <svg class="mx-auto mb-2 h-10 w-10 text-mist" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17h14l1.2-4.4a3 3 0 00-1.4-3.4l-2.1-1.1-1.5-2.2A3 3 0 0012.7 5H9.5a3 3 0 00-2.4 1.2L5.6 8.1 3.8 9a3 3 0 00-1.6 3.3L3.4 17H5z"></path></svg>
                    No hay vehículos dentro
                </div>
                <ul class="mt-3 space-y-2" x-show="proximasSalidas.length > 0">
                    <template x-for="v in proximasSalidas" :key="v.id">
                        <li class="group flex items-center justify-between gap-3 rounded-xl border border-mist/80 bg-white/80 px-3 py-2.5 transition hover:border-brand-blue-light/40 hover:bg-brand-blue-soft/20">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="grid h-8 w-8 flex-shrink-0 place-items-center rounded-lg bg-night-900 font-mono text-[0.6rem] font-semibold text-brand-blue-light"
                                      x-text="v.placa?.slice(-2)"></span>
                                <div class="min-w-0">
                                    <p class="truncate font-mono text-xs font-semibold tracking-wider text-steel-900" x-text="v.placa"></p>
                                    <p class="truncate text-[0.68rem] text-steel-500" x-text="v.clienteNombre"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-brand-blue" x-text="v.tiempoDentro"></p>
                                <p class="text-[0.65rem] capitalize text-steel-500" x-text="v.tamano"></p>
                            </div>
                        </li>
                    </template>
                </ul>
                <a href="/vehiculos" class="mt-3 flex items-center justify-center gap-1.5 rounded-lg border border-dashed border-mist py-2 text-[0.7rem] font-semibold text-brand-blue transition hover:border-brand-blue-light/50 hover:bg-brand-blue-soft/30">
                    Ver todos los vehículos
                </a>
            </div>
        </div>
    </div>

    <!-- Actividad -->
    <div class="surface p-5 md:p-6" x-ref="recentActivity">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Actividad reciente</h2>
                <p class="mt-1 text-xs text-steel-500">Últimos movimientos registrados en el sistema</p>
            </div>
            <a href="/auditoria" class="btn btn-secondary py-2 text-xs">Ver auditoría</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[560px]">
                <thead>
                    <tr class="border-b border-mist text-left">
                        <th class="px-3 pb-2.5">Hora</th>
                        <th class="px-3 pb-2.5">Usuario</th>
                        <th class="px-3 pb-2.5">Acción</th>
                        <th class="px-3 pb-2.5">Entidad</th>
                        <th class="px-3 pb-2.5">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist/70">
                    <template x-for="log in ultimosLogs.slice(0, 8)" :key="log.id">
                        <tr class="hover:bg-brand-blue-soft/25">
                            <td class="px-3 py-2.5 text-xs text-steel-500" x-text="utils.formatDate(log.timestamp)"></td>
                            <td class="px-3 py-2.5 text-xs font-medium text-steel-900" x-text="log.usuarioNombre"></td>
                            <td class="px-3 py-2.5">
                                <span class="chip" :class="log.accion === 'crear' ? 'chip-blue' : log.accion === 'anular' ? 'chip-red' : 'chip-green'" x-text="log.accion"></span>
                            </td>
                            <td class="px-3 py-2.5 text-xs capitalize text-steel-700" x-text="log.entidad"></td>
                            <td class="max-w-[220px] truncate px-3 py-2.5 font-mono text-[0.68rem] text-steel-500" x-text="log.entidadId"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function dashboard() {
        return {
            stats: {
                facturasHoy: 0,
                vehiculosDentro: 0,
                lavadosHoy: 0,
                stockBajo: 0,
                ingresoHoy: 0,
                comisionHoy: 0,
                pendientes: 0
            },
            todayLabel: new Date().toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }),
            chartPeriod: '7',
            chart: null,
            proximasSalidas: [],
            ultimosLogs: [],
            
            init() {
                this.loadStats();
                this.loadProximasSalidas();
                this.loadUltimosLogs();
                this.initChart();
                
                // Animate entrance
                this.$nextTick(() => {
                    this.animateEntrance();
                });
            },
            
            animateEntrance() {
                if (window.prefersReducedMotion?.()) return;
                
                gsap.from(this.$refs.kpiGrid.children, { opacity: 0, y: 20, duration: 0.4, stagger: 0.08, ease: 'power2.out' });
                gsap.from([this.$refs.chartCard, this.$refs.quickActions, this.$refs.proximasSalidas, this.$refs.recentActivity], { opacity: 0, y: 20, duration: 0.4, stagger: 0.1, ease: 'power2.out', delay: 0.2 });
            },
            
            loadStats() {
                const facturas = this.$store.facturas.getStats();
                const lavados = this.$store.lavados.getStats();
                this.stats.facturasHoy = facturas.totalHoy;
                this.stats.vehiculosDentro = this.$store.vehiculos.getStats().dentro;
                this.stats.lavadosHoy = lavados.totalHoy;
                this.stats.stockBajo = this.$store.inventario.getStats().stockBajo;
                this.stats.ingresoHoy = lavados.ingresoHoy;
                this.stats.comisionHoy = lavados.comisionHoy;
                this.stats.pendientes = facturas.totalPendiente;
            },
            
            loadProximasSalidas() {
                const dentro = this.$store.vehiculos.getDentro();
                this.proximasSalidas = dentro.slice(0, 5).map(v => {
                    const entrada = new Date(v.entrada.fechaEntrada);
                    const ahora = new Date();
                    const diffMs = ahora - entrada;
                    const horas = Math.floor(diffMs / 3600000);
                    const mins = Math.floor((diffMs % 3600000) / 60000);
                    return {
                        ...v,
                        tiempoDentro: `${horas}h ${mins}m`
                    };
                });
            },
            
            loadUltimosLogs() {
                this.ultimosLogs = this.$store.auditoria.getAll().slice(0, 10);
            },
            
            initChart() {
                this.$nextTick(() => {
                    const ctx = this.$refs.chartCanvas;
                    if (!ctx) return;
                    
                    // Simple canvas chart (no external library)
                    this.drawChart(ctx);
                });
            },
            
            drawChart(canvas) {
                const ctx = canvas.getContext('2d');
                const data = this.getChartData();
                const ratio = window.devicePixelRatio || 1;
                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;
                canvas.width = width * ratio;
                canvas.height = height * ratio;
                const padding = { top: 18, right: 14, bottom: 30, left: 38 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;

                ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
                ctx.clearRect(0, 0, width, height);

                const points = data.values.map((val, i) => ({ val: val, i: i }));
                const maxRaw = Math.max(...data.values, 1);
                const maxVal = Math.max(5, Math.ceil(maxRaw / 5) * 5);
                const pointX = (i) => padding.left + (chartWidth / Math.max(points.length - 1, 1)) * i;
                const pointY = (val) => padding.top + chartHeight - (val / maxVal) * chartHeight;

                // Rejilla y etiquetas del eje Y
                ctx.strokeStyle = 'rgba(38, 63, 155, 0.09)';
                ctx.lineWidth = 1;
                ctx.setLineDash([4, 7]);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px "Space Grotesk", system-ui, sans-serif';
                ctx.textAlign = 'right';
                for (let i = 0; i <= 5; i++) {
                    const y = padding.top + (chartHeight / 5) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();
                    const value = Math.round(maxVal - (maxVal / 5) * i);
                    ctx.fillText(String(value), padding.left - 10, y + 3.5);
                }
                ctx.setLineDash([]);

                if (points.length === 0) return;

                // Trazo suavizado
                const path = () => {
                    ctx.beginPath();
                    ctx.moveTo(pointX(0), pointY(points[0].val));
                    for (let i = 1; i < points.length; i++) {
                        const prevX = pointX(i - 1);
                        const prevY = pointY(points[i - 1].val);
                        const currX = pointX(i);
                        const currY = pointY(points[i].val);
                        const midX = (prevX + currX) / 2;
                        ctx.bezierCurveTo(midX, prevY, midX, currY, currX, currY);
                    }
                };

                // Relleno degradado
                const area = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
                area.addColorStop(0, 'rgba(18, 178, 236, 0.24)');
                area.addColorStop(1, 'rgba(18, 178, 236, 0)');
                path();
                ctx.lineTo(pointX(points.length - 1), height - padding.bottom);
                ctx.lineTo(pointX(0), height - padding.bottom);
                ctx.closePath();
                ctx.fillStyle = area;
                ctx.fill();

                // Línea
                const stroke = ctx.createLinearGradient(padding.left, 0, width - padding.right, 0);
                stroke.addColorStop(0, '#12b2ec');
                stroke.addColorStop(1, '#263f9b');
                path();
                ctx.strokeStyle = stroke;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();

                // Puntos
                points.forEach((point, i) => {
                    const x = pointX(i);
                    const y = pointY(point.val);
                    const isLast = i === points.length - 1;
                    ctx.beginPath();
                    ctx.arc(x, y, isLast ? 5 : 3.5, 0, Math.PI * 2);
                    ctx.fillStyle = '#ffffff';
                    ctx.fill();
                    ctx.lineWidth = 2.5;
                    ctx.strokeStyle = '#263f9b';
                    ctx.stroke();
                });

                // Etiquetas del eje X (máximo 7)
                ctx.fillStyle = '#94a3b8';
                ctx.font = '10px "Space Grotesk", system-ui, sans-serif';
                ctx.textAlign = 'center';
                const step = Math.max(1, Math.ceil(data.labels.length / 7));
                data.labels.forEach((label, i) => {
                    if (i % step !== 0 && i !== data.labels.length - 1) return;
                    const x = padding.left + (chartWidth / Math.max(data.labels.length - 1, 1)) * i;
                    ctx.fillText(label, x, height - 8);
                });
            },
            
            getChartData() {
                const days = parseInt(this.chartPeriod);
                const labels = [];
                const values = [];
                
                for (let i = days - 1; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    labels.push(date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' }));
                    
                    const dayStr = date.toDateString();
                    const count = this.$store.lavados.items.filter(l => 
                        new Date(l.fecha).toDateString() === dayStr && l.estado === 'completado'
                    ).length;
                    values.push(count);
                }
                
                return { labels, values };
            }
        }
    }
</script>
@endsection