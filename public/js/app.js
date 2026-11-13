// Arranque del frontend (sin bundler).
// Orden de carga en el layout: gsap, ScrollTrigger, Flip, Alpine, stores, components, animations, app.

window.prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.Flip = Flip;

// Utilidades también como global: varias vistas las usan como `utils.formatDate(...)`
window.utils = {
    formatDate: window.formatDate,
    formatDateShort: window.formatDateShort,
    formatCurrency: window.formatCurrency,
    uid: window.uid
};

// Stores y estado global de la UI dentro de Alpine.
document.addEventListener('alpine:init', () => {
    // Navegación / layout
    Alpine.store('sidebar', {
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebarCollapsed', this.collapsed);
        }
    });

    // Datos (localStorage por ahora, pendiente de backend)
    Alpine.store('auth', window.Stores.auth);
    Alpine.store('facturas', window.Stores.facturas);
    Alpine.store('clientes', window.Stores.clientes);
    Alpine.store('empleados', window.Stores.empleados);
    Alpine.store('vehiculos', window.Stores.vehiculos);
    Alpine.store('lavados', window.Stores.lavados);
    Alpine.store('inventario', window.Stores.inventario);
    Alpine.store('auditoria', window.Stores.auditoria);
    Alpine.store('settings', window.Stores.settings);
    Alpine.store('users', window.Stores.users);

    // Utilidades
    Alpine.store('utils', {
        formatDate: window.formatDate,
        formatDateShort: window.formatDateShort,
        formatCurrency: window.formatCurrency,
        uid: window.uid
    });

    // UI global
    Alpine.store('toast', window.Toast);
    Alpine.store('modal', window.Modal);
    Alpine.store('commandPalette', window.CommandPalette);

    // Accesos usados por los scripts inline de las vistas
    window.CommandPaletteInstance = Alpine.store('commandPalette');
    window.ToastStore = Alpine.store('toast');
    window.ModalStore = Alpine.store('modal');
    window.AuthStore = Alpine.store('auth');
});

// Acciones rápidas del command palette (Cmd/Ctrl+K)
document.addEventListener('DOMContentLoaded', () => {
    // Entrada del shell
    if (!window.prefersReducedMotion()) {
        gsap.from('.app-sidebar', { x: -18, opacity: 0, duration: 0.5, ease: 'power3.out' });
        gsap.from('.app-sidebar .nav-link', { x: -10, opacity: 0, duration: 0.4, stagger: 0.03, delay: 0.12, ease: 'power2.out' });
        gsap.from('.app-topbar', { y: -14, opacity: 0, duration: 0.45, ease: 'power3.out' });
        gsap.from('.page-shell', { y: 14, opacity: 0, duration: 0.5, delay: 0.1, ease: 'power2.out' });
    }

    window.CommandPalette.register([
        { label: 'Nueva factura', description: 'Crear factura', action: () => (window.location.href = '/facturacion/create') },
        { label: 'Registrar entrada vehículo', description: 'Entrada de vehículo', action: () => (window.location.href = '/vehiculos/entrada') },
        { label: 'Registrar lavado', description: 'Nuevo lavado', action: () => (window.location.href = '/lavados/create') },
        { label: 'Nuevo empleado', description: 'Alta de personal', action: () => (window.location.href = '/empleados/create') },
        { label: 'Planificación de vehículos', description: 'Orden de atención', action: () => (window.location.href = '/vehiculos/planificacion') },
        { label: 'Asignación de trabajadores', description: 'Planificación de lavados', action: () => (window.location.href = '/lavados/planificacion') },
        { label: 'Nuevo producto', description: 'Inventario', action: () => (window.location.href = '/inventario/create') },
        { label: 'Dashboard', description: 'Ir al dashboard', action: () => (window.location.href = '/dashboard') },
        { label: 'Ajustes', description: 'Configuración', action: () => (window.location.href = '/ajustes') },
        { label: 'Mi perfil', description: 'Perfil de usuario', action: () => (window.location.href = '/perfil') }
    ]);
});
