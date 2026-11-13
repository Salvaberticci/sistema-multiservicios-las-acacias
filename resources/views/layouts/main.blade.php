<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Multiservicios Las Acacias')</title>
    <meta name="description" content="Sistema de gestión para Multiservicios Las Acacias">
    <script>
        // Guardia de sesión demo: sin usuario no se muestra el panel (solo login).
        (function () {
            try {
                var session = JSON.parse(localStorage.getItem('auth') || 'null');
                if (!session || !session.user) window.location.replace('/login');
            } catch (e) {
                window.location.replace('/login');
            }
        })();
    </script>
    @include('layouts.partials.head')
    @stack('styles')
</head>
<body class="h-full font-sans antialiased" x-data="mainLayout()" @keydown.escape.window="closeModals()">
    <!-- Toasts -->
    <div id="toast-container" class="fixed top-4 right-4 z-[80] flex flex-col gap-2 pointer-events-none"></div>

    <!-- Command Palette -->
    <div x-show="commandPalette.open" x-transition.opacity
         class="fixed inset-0 z-[70] bg-night-950/75 backdrop-blur-sm" @click="commandPalette.close()" style="display: none;"></div>
    <div x-show="commandPalette.open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed inset-x-0 top-[12%] z-[71] mx-auto w-full max-w-2xl px-4" style="display: none;">
        <div class="topbar-panel">
            <div class="flex items-center gap-3 px-4 py-3.5 border-b border-white/8">
                <svg class="w-5 h-5 text-brand-blue-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" data-command-search x-model="commandPalette.search" @keydown="commandPalette.handleKeydown($event)"
                       placeholder="Buscar o ejecutar un comando…" autocomplete="off"
                       class="flex-1 bg-transparent border-none outline-none text-[0.95rem] text-white placeholder-white/35">
                <kbd class="px-2 py-1 text-[0.62rem] font-semibold tracking-wider bg-white/6 border border-white/10 rounded-md text-white/50">ESC</kbd>
            </div>
            <div class="max-h-96 overflow-y-auto py-1">
                <template x-for="(item, index) in commandPalette.filteredItems" :key="item.label">
                    <button @click="commandPalette.select(item)"
                            :class="commandPalette.selectedIndex === index ? 'bg-brand-blue-light/12 text-white' : 'text-white/75'"
                            class="w-full px-4 py-2.5 text-left hover:bg-white/5 transition-colors flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-white/5 border border-white/8 text-brand-blue-light">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        <span class="font-medium text-sm" x-text="item.label"></span>
                        <span class="text-xs text-white/40 ml-auto" x-text="item.description"></span>
                    </button>
                </template>
                <div x-show="commandPalette.filteredItems.length === 0" class="px-4 py-10 text-center text-white/45 text-sm">No se encontraron comandos</div>
            </div>
            <div class="flex items-center justify-between px-4 py-2 border-t border-white/8 text-[0.65rem] text-white/40">
                <span>Navega con ↑ ↓ · Enter para ejecutar</span>
                <span class="tracking-[0.18em] uppercase">Multiservicios Las Acacias</span>
            </div>
        </div>
    </div>

    <div class="app-shell flex min-h-screen">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-night-950/70 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>
        <!-- Sidebar -->
        <aside class="app-sidebar fixed inset-y-0 left-0 z-50 h-screen lg:sticky lg:top-0"
               :class="{ 'is-collapsed': sidebar.collapsed, 'is-open': sidebarOpen }">
            <div class="relative z-10 flex items-center gap-3 px-5 h-[4.6rem] border-b border-white/5">
                <a href="/dashboard" class="flex items-center min-w-0" title="Multiservicios Las Acacias">
                    <span class="brand-tile"><img src="/logo.jpeg" alt="Multiservicios Las Acacias"></span>
                </a>
                <button @click="sidebar.toggle()" class="icon-btn hidden lg:grid ml-auto" :title="sidebar.collapsed ? 'Expandir menú' : 'Colapsar menú'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </button>
                <button @click="sidebarOpen = false" class="icon-btn lg:hidden ml-auto" title="Cerrar menú">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="relative z-10 flex-1 overflow-y-auto px-3 py-5 space-y-1">
                <p class="nav-group-label px-2 mb-2" x-show="!sidebar.collapsed">Operación</p>
                <template x-for="item in navigationItems.filter(i => i.group !== 'control')" :key="item.label">
                    <a :href="item.href" class="nav-link" :class="{ 'is-active': isActive(item.href), 'justify-center': sidebar.collapsed }"
                       x-show="!item.roles || auth.hasRole(item.roles)" :title="item.label">
                        <span class="nav-icon" x-html="item.icon"></span>
                        <span class="flex-1 truncate" x-show="!sidebar.collapsed" x-text="item.label"></span>
                        <span x-show="item.badge && !sidebar.collapsed" class="chip chip-red" x-text="item.badge"></span>
                        <span x-show="item.badge && sidebar.collapsed" class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-brand-red"></span>
                    </a>
                </template>

                <p class="nav-group-label px-2 pt-5 mb-2" x-show="!sidebar.collapsed">Control</p>
                <template x-for="item in navigationItems.filter(i => i.group === 'control')" :key="item.label">
                    <a :href="item.href" class="nav-link" :class="{ 'is-active': isActive(item.href), 'justify-center': sidebar.collapsed }"
                       x-show="!item.roles || auth.hasRole(item.roles)" :title="item.label">
                        <span class="nav-icon" x-html="item.icon"></span>
                        <span class="flex-1 truncate" x-show="!sidebar.collapsed" x-text="item.label"></span>
                    </a>
                </template>
            </nav>

            <svg class="car-watermark hidden lg:block" viewBox="0 0 220 80" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 58h12l6-15h24l14-10c5-3.5 10-4 16-4h20c9 0 16 3 23 9l10 9 18 4c9 2 14 4 14 7"></path>
                <path d="M54 43h30l8-12"></path>
                <path d="M96 31h26c7 0 12 2 17 7l6 5"></path>
                <circle cx="60" cy="58" r="11"></circle>
                <circle cx="172" cy="58" r="11"></circle>
                <path d="M4 58h6m164 0h22"></path>
            </svg>

            <div class="relative z-10 px-3 pb-3" x-show="!sidebar.collapsed">
                <div class="sidebar-status">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[0.7rem] font-semibold text-white/80">
                            <span class="status-dot"></span> Sistema en línea
                        </span>
                        <span class="text-[0.58rem] tracking-[0.2em] text-brand-blue-light/70">v1.0</span>
                    </div>
                    <div class="mt-2.5 h-1 rounded-full bg-white/8 overflow-hidden">
                        <span class="block h-full w-2/3 rounded-full bg-gradient-to-r from-brand-blue-light to-brand-blue"></span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 border-t border-white/5 p-3">
                <div class="flex items-center gap-3" x-show="!sidebar.collapsed">
                    <div class="w-9 h-9 rounded-xl grid place-items-center bg-gradient-to-br from-brand-blue-light/80 to-brand-blue text-white font-semibold text-sm shadow-glow-blue"
                         x-text="auth.user?.name?.charAt(0) || 'U'"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[0.82rem] font-semibold text-white truncate" x-text="auth.user?.name || 'Invitado'"></p>
                        <p class="text-[0.62rem] uppercase tracking-[0.16em] text-brand-blue-light/70" x-text="auth.user?.role || 'sin sesión'"></p>
                    </div>
                    <form action="/logout" method="POST" @submit="auth.logout()">
                        @csrf
                        <button type="submit" class="icon-btn" title="Cerrar sesión">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
                <div x-show="sidebar.collapsed" class="grid place-items-center">
                    <div class="w-9 h-9 rounded-xl grid place-items-center bg-gradient-to-br from-brand-blue-light/80 to-brand-blue text-white font-semibold text-sm"
                         x-text="auth.user?.name?.charAt(0) || 'U'"></div>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <header class="app-topbar sticky top-0">
                <div class="flex items-center justify-between gap-3 h-[4.1rem] px-4 md:px-6">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = true" class="icon-btn lg:hidden" aria-label="Abrir menú">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <span class="hidden xl:inline-flex chip chip-dark">
                            <span class="status-dot"></span> En vivo
                        </span>
                        <nav class="hidden md:flex items-center gap-1" aria-label="Breadcrumb">
                            <template x-for="(crumb, index) in breadcrumbs" :key="index + '-' + crumb.label">
                                <span class="flex items-center gap-1">
                                    <a :href="crumb.href || '/dashboard'" class="crumb-link" x-show="index < breadcrumbs.length - 1" x-text="crumb.label"></a>
                                    <span class="text-white/20" x-show="index < breadcrumbs.length - 1">/</span>
                                    <span class="crumb-current" x-show="index === breadcrumbs.length - 1" x-text="crumb.label"></span>
                                </span>
                            </template>
                        </nav>
                    </div>

                    <div class="flex items-center gap-2 md:gap-3">
                        <button class="topbar-search hidden md:flex" @click="commandPalette.toggle()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span class="flex-1 text-left">Buscar o ejecutar…</span>
                            <kbd class="px-1.5 py-0.5 text-[0.62rem] font-semibold bg-white/8 border border-white/10 rounded text-white/55">⌘K</kbd>
                        </button>

                        <!-- Notificaciones -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="icon-btn relative" aria-label="Notificaciones">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1"></path></svg>
                                <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-brand-red shadow-[0_0_10px_rgba(221,32,36,0.9)]"></span>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 @click.outside="open = false" class="absolute right-0 mt-2 w-80 topbar-panel z-50" style="display: none;">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-white/8">
                                    <span class="text-sm font-semibold text-white">Notificaciones</span>
                                    <span class="chip chip-blue">3 nuevas</span>
                                </div>
                                <div class="divide-y divide-white/5">
                                    <a href="/facturacion" class="topbar-panel-item">
                                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-brand-blue-light/12 text-brand-blue-light"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"></path></svg></span>
                                        <span class="flex-1 min-w-0"><span class="block text-[0.8rem] font-medium text-white/90">Nueva factura emitida</span><span class="block text-[0.7rem] text-white/45">Hace 10 min · cobro registrado</span></span>
                                    </a>
                                    <a href="/inventario" class="topbar-panel-item">
                                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-brand-red/12 text-brand-red-light"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.3 19h15.4a1 1 0 00.9-1.4L13.7 4.6a1 1 0 00-1.7 0L5 17.6A1 1 0 005.9 19z"></path></svg></span>
                                        <span class="flex-1 min-w-0"><span class="block text-[0.8rem] font-medium text-white/90">Stock bajo detectado</span><span class="block text-[0.7rem] text-white/45">Hace 1 h · revisar inventario</span></span>
                                    </a>
                                    <a href="/vehiculos" class="topbar-panel-item">
                                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-emerald-400/12 text-emerald-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                        <span class="flex-1 min-w-0"><span class="block text-[0.8rem] font-medium text-white/90">Salida de vehículo</span><span class="block text-[0.7rem] text-white/45">Hace 2 h · 1234ABC</span></span>
                                    </a>
                                </div>
                                <a href="/auditoria" class="block px-4 py-2.5 text-center text-xs font-semibold text-brand-blue-light hover:bg-white/5 transition">Ver actividad completa</a>
                            </div>
                        </div>

                        <span class="hidden md:block w-px h-7 bg-white/10"></span>

                        <!-- Usuario -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2.5 pl-1 pr-2 py-1 rounded-xl hover:bg-white/6 transition" aria-label="Usuario">
                                <span class="w-8 h-8 rounded-lg grid place-items-center bg-gradient-to-br from-brand-blue-light/90 to-brand-blue text-white text-xs font-bold shadow-glow-blue"
                                      x-text="auth.user?.name?.charAt(0) || 'U'"></span>
                                <span class="hidden md:block text-left leading-tight">
                                    <span class="block text-[0.78rem] font-semibold text-white/90" x-text="auth.user?.name || 'Invitado'"></span>
                                    <span class="block text-[0.6rem] uppercase tracking-[0.14em] text-brand-blue-light/70" x-text="auth.user?.role || 'sin sesión'"></span>
                                </span>
                                <svg class="w-3.5 h-3.5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                 @click.outside="open = false" class="absolute right-0 mt-2 w-52 topbar-panel z-50 py-1" style="display: none;">
                                <a href="/perfil" class="topbar-panel-item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Mi perfil</a>
                                <a href="/ajustes" class="topbar-panel-item"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.3 4.3c.4-1.8 2.9-1.8 3.3 0a1.7 1.7 0 002.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 001 2.5c1.8.4 1.8 2.9 0 3.3a1.7 1.7 0 00-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 00-2.6 1c-.4 1.8-2.9 1.8-3.3 0a1.7 1.7 0 00-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 00-1-2.6c-1.8-.4-1.8-2.9 0-3.3a1.7 1.7 0 001-2.5c-.9-1.6.9-3.3 2.4-2.4a1.7 1.7 0 002.6-1.1z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Ajustes</a>
                                <div class="my-1 h-px bg-white/8"></div>
                                <form action="/logout" method="POST" @submit="auth.logout()">
                                    @csrf
                                    <button type="submit" class="topbar-panel-item text-brand-red-light"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg> Cerrar sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="app-main flex-1 p-4 md:p-6 lg:p-8">
                <div class="page-shell mx-auto w-full max-w-[1500px]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @yield('scripts')

    @include('layouts.partials.scripts')

    <script>
        function mainLayout() {
            return {
                sidebarOpen: false,
                breadcrumbs: [],
                navigationItems: [
                    { label: 'Dashboard', href: '/dashboard', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>', group: 'operacion' },
                    { label: 'Facturación', href: '/facturacion', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', roles: ['admin', 'operador', 'cajero'], group: 'operacion' },
                    { label: 'Inventario', href: '/inventario', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>', roles: ['admin', 'operador'], group: 'operacion' },
                    { label: 'Vehículos', href: '/vehiculos', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17h14l1.2-4.4a3 3 0 00-1.4-3.4l-2.1-1.1-1.5-2.2A3 3 0 0012.7 5H9.5a3 3 0 00-2.4 1.2L5.6 8.1 3.8 9a3 3 0 00-1.6 3.3L3.4 17H5z"></path><circle cx="7.5" cy="17.5" r="1.8"></circle><circle cx="16.5" cy="17.5" r="1.8"></circle></svg>', roles: ['admin', 'operador', 'cajero'], group: 'operacion' },
                    { label: 'Lavados', href: '/lavados', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1m0-12.8l-2.1 2.1m-8.6 8.6l-2.1 2.1"></path><circle cx="12" cy="12" r="3.5"></circle></svg>', roles: ['admin', 'operador'], group: 'operacion' },
                    { label: 'Empleados', href: '/empleados', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a3 3 0 10-2.5-4.5"></path></svg>', roles: ['admin', 'operador'], group: 'operacion' },
                    { label: 'Auditoría', href: '/auditoria', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.6-4A11.9 11.9 0 0112 2.9 11.9 11.9 0 013.4 6 12 12 0 003 9c0 5.6 3.8 10.3 9 11.6 5.2-1.3 9-6 9-11.6 0-1-.1-2.1-.4-3z"></path></svg>', roles: ['admin', 'auditor'], group: 'control' },
                    { label: 'Seguridad', href: '/seguridad', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 10-8 0v4M5 11h14v9a1 1 0 01-1 1H6a1 1 0 01-1-1v-9z"></path></svg>', roles: ['admin'], group: 'control' },
                    { label: 'Mi perfil', href: '/perfil', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>', group: 'control' },
                    { label: 'Ajustes', href: '/ajustes', icon: '<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.3 4.3c.4-1.8 2.9-1.8 3.3 0a1.7 1.7 0 002.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 001 2.5c1.8.4 1.8 2.9 0 3.3a1.7 1.7 0 00-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 00-2.6 1c-.4 1.8-2.9 1.8-3.3 0a1.7 1.7 0 00-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 00-1-2.6c-1.8-.4-1.8-2.9 0-3.3a1.7 1.7 0 001-2.5c-.9-1.6.9-3.3 2.4-2.4a1.7 1.7 0 002.6-1.1z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>', roles: ['admin'], group: 'control' }
                ],
                auth: window.Alpine.store('auth'),
                sidebar: window.Alpine.store('sidebar'),
                commandPalette: window.Alpine.store('commandPalette'),

                isActive(href) {
                    const path = window.location.pathname;
                    if (href === '/dashboard') return path === '/dashboard';
                    return path === href || path.startsWith(href + '/');
                },

                closeModals() {
                    this.sidebarOpen = false;
                    if (this.commandPalette.open) this.commandPalette.close();
                },

                init() {
                    this.updateBreadcrumbs();
                    window.addEventListener('popstate', () => this.updateBreadcrumbs());
                },

                updateBreadcrumbs() {
                    const path = window.location.pathname;
                    const segments = path.split('/').filter(Boolean).filter(s => s !== 'dashboard');
                    this.breadcrumbs = [{ label: 'Dashboard', href: '/dashboard' }];

                    const translations = {
                        facturacion: 'Facturación',
                        inventario: 'Inventario',
                        vehiculos: 'Vehículos',
                        lavados: 'Lavados',
                        auditoria: 'Auditoría',
                        seguridad: 'Seguridad',
                        perfil: 'Mi perfil',
                        ajustes: 'Ajustes',
                        create: 'Nuevo',
                        edit: 'Editar',
                        entrada: 'Entrada',
                        salida: 'Salida'
                    };

                    let currentPath = '';
                    segments.forEach((segment, index) => {
                        currentPath += '/' + segment;
                        const isLast = index === segments.length - 1;
                        let label = segment.charAt(0).toUpperCase() + segment.slice(1).replace('-', ' ');
                        label = translations[segment] || label;
                        this.breadcrumbs.push({ label, href: isLast ? null : currentPath });
                    });
                }
            }
        }
    </script>
</body>
</html>
