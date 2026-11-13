// Componentes globales (Alpine + GSAP). Requiere gsap.min.js y ScrollTrigger.min.js cargados antes.
gsap.registerPlugin(ScrollTrigger);

// ============================================
// LOGIN FORM COMPONENT
// ============================================
window.loginForm = () => ({
    form: {
        email: '',
        password: '',
        remember: false
    },
    errors: {},
    loading: false,
    showPassword: false,
    cardRef: null,
    logoRef: null,
    demoRef: null,
    submitBtnRef: null,

    init() {
        // GSAP animations for login card
        if (!window.prefersReducedMotion?.()) {
            gsap.fromTo(this.$refs.card, 
                { opacity: 0, y: 30 },
                { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' }
            );
            gsap.fromTo(this.$refs.logo,
                { opacity: 0, scale: 0.8 },
                { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.7)', delay: 0.1 }
            );
            gsap.fromTo(this.$refs.demo,
                { opacity: 0, y: 10 },
                { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out', delay: 0.3 }
            );
        }
    },

    validate(field) {
        const value = this.form[field];
        if (field === 'email') {
            if (!value) this.errors.email = 'El email es obligatorio';
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) this.errors.email = 'Email inválido';
            else delete this.errors.email;
        }
        if (field === 'password') {
            if (!value) this.errors.password = 'La contraseña es obligatoria';
            else if (value.length < 4) this.errors.password = 'Mínimo 4 caracteres';
            else delete this.errors.password;
        }
    },

    clearError(field) {
        delete this.errors[field];
    },

    fillDemo(cred) {
        this.form.email = cred.email;
        this.form.password = cred.password;
    },

    async submit() {
        this.validate('email');
        this.validate('password');
        
        if (this.errors.email || this.errors.password) return;

        this.loading = true;
        
        try {
            // Use the global auth store
            if (window.Stores && window.Stores.auth) {
                await window.Stores.auth.login(this.form.email, this.form.password);
                if (window.Stores.auth.isAuthenticated) {
                    window.Toast?.success('Bienvenido a Multiservicios Las Acacias');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 800);
                    return;
                }
            }
            
            // Fallback if store not available
            this.errors.general = 'Error de autenticación';
        } catch (e) {
            console.error(e);
            this.errors.general = 'Credenciales inválidas';
        } finally {
            this.loading = false;
        }
    }
});
// ============================================
// TOAST / NOTIFICACIONES (Sonner-style)
// ============================================
window.Toast = {
    toasts: [],
    container: null,
    
    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(this.container);
        }
    },
    
    show(message, type = 'info', duration = 4000) {
        this.init();
        
        const accents = {
            success: '#22c55e',
            error: '#dd2024',
            warning: '#f59e0b',
            info: '#12b2ec'
        };
        
        const icons = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        const toast = document.createElement('div');
        toast.className = 'relative overflow-hidden flex items-center gap-3 px-4 py-3 rounded-xl min-w-[280px] max-w-md pointer-events-auto border border-white/10 bg-night-900/95 text-white shadow-[0_30px_60px_-25px_rgba(2,6,20,0.9)] backdrop-blur';
        toast.style.borderLeft = `3px solid ${accents[type]}`;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.innerHTML = `
            <span class="flex-shrink-0" style="color:${accents[type]}">${icons[type]}</span>
            <span class="flex-1 text-sm text-white/90">${message}</span>
            <button class="flex-shrink-0 text-lg text-white/40 hover:text-white transition" onclick="Toast.remove(this.closest('[data-toast-id]'))">&times;</button>
            <div data-progress class="absolute bottom-0 left-0 h-[3px]" style="width: 100%; transform-origin: left; transform: scaleX(1); background: linear-gradient(90deg, ${accents[type]}, rgba(255,255,255,0.25));"></div>
        `;
        
        const progressBar = toast.querySelector('[data-progress]');
        toast.dataset.toastId = Date.now() + Math.random();
        
        this.container.appendChild(toast);
        this.toasts.push(toast);
        
        // Animate in
        gsap.to(toast, { opacity: 1, x: 0, duration: 0.35, ease: 'power2.out' });
        
        // Progress bar
        gsap.to(progressBar, { scaleX: 0, duration: duration / 1000, ease: 'none' });
        
        // Auto remove
        setTimeout(() => this.remove(toast), duration);
        
        return toast;
    },
    
    remove(toast) {
        if (!toast || toast._removing) return;
        toast._removing = true;
        
        gsap.to(toast, {
            opacity: 0,
            x: 300,
            duration: 0.25,
            ease: 'power2.in',
            onComplete: () => {
                toast.remove();
                this.toasts = this.toasts.filter(t => t !== toast);
            }
        });
    },
    
    success(msg, dur) { return this.show(msg, 'success', dur); },
    error(msg, dur) { return this.show(msg, 'error', dur); },
    warning(msg, dur) { return this.show(msg, 'warning', dur); },
    info(msg, dur) { return this.show(msg, 'info', dur); }
};

// ============================================
// MODAL MANAGER
// ============================================
window.Modal = {
    openModals: [],
    
    open(modalEl) {
        if (!modalEl) return;
        
        const backdrop = modalEl.querySelector('[data-modal-backdrop]') || modalEl.previousElementSibling;
        modalEl.style.display = 'flex';
        modalEl._backdrop = backdrop;
        
        this.openModals.push(modalEl);
        document.body.style.overflow = 'hidden';
        
        // Focus trap
        const focusable = modalEl.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        modalEl._firstFocusable = focusable[0];
        modalEl._lastFocusable = focusable[focusable.length - 1];
        modalEl._firstFocusable?.focus();
        
        modalEl.addEventListener('keydown', this.trapFocus);
        
        // GSAP animation
        if (window.prefersReducedMotion?.()) {
            gsap.set(modalEl, { opacity: 1 });
            gsap.set(backdrop, { opacity: 1 });
        } else {
            gsap.fromTo(modalEl.querySelector('[data-modal-content]') || modalEl, 
                { scale: 0.95, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.2, ease: 'power2.out' }
            );
            gsap.fromTo(backdrop, { opacity: 0 }, { opacity: 1, duration: 0.15 });
        }
    },
    
    close(modalEl) {
        if (!modalEl) return;
        
        const backdrop = modalEl._backdrop;
        modalEl.removeEventListener('keydown', this.trapFocus);
        
        const done = () => {
            modalEl.style.display = 'none';
            this.openModals = this.openModals.filter(m => m !== modalEl);
            if (this.openModals.length === 0) document.body.style.overflow = '';
        };
        
        if (window.prefersReducedMotion?.()) {
            done();
        } else {
            gsap.to(modalEl.querySelector('[data-modal-content]') || modalEl, {
                scale: 0.95, opacity: 0, duration: 0.15, ease: 'power2.in', onComplete: done
            });
            gsap.to(backdrop, { opacity: 0, duration: 0.1 });
        }
    },
    
    trapFocus(e) {
        if (e.key !== 'Tab') return;
        const modal = this.openModals[this.openModals.length - 1];
        if (!modal) return;
        
        if (e.shiftKey) {
            if (document.activeElement === modal._firstFocusable) {
                e.preventDefault();
                modal._lastFocusable?.focus();
            }
        } else {
            if (document.activeElement === modal._lastFocusable) {
                e.preventDefault();
                modal._firstFocusable?.focus();
            }
        }
    }
};

// Close on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && window.Modal.openModals.length > 0) {
        window.Modal.close(window.Modal.openModals[window.Modal.openModals.length - 1]);
    }
});

// ============================================
// TABLE HELPER (Alpine component)
// ============================================
window.TableComponent = (initialData = [], columns = []) => ({
    data: initialData,
    columns,
    sortKey: null,
    sortDir: 'asc',
    search: '',
    selected: [],
    page: 1,
    perPage: 10,
    
    get filteredData() {
        let result = this.data;
        
        if (this.search) {
            const q = this.search.toLowerCase();
            result = result.filter(row => 
                this.columns.some(col => 
                    String(row[col.key] || '').toLowerCase().includes(q)
                )
            );
        }
        
        if (this.sortKey) {
            result = [...result].sort((a, b) => {
                const av = a[this.sortKey];
                const bv = b[this.sortKey];
                const dir = this.sortDir === 'asc' ? 1 : -1;
                if (av < bv) return -1 * dir;
                if (av > bv) return 1 * dir;
                return 0;
            });
        }
        
        return result;
    },
    
    get paginatedData() {
        const start = (this.page - 1) * this.perPage;
        return this.filteredData.slice(start, start + this.perPage);
    },
    
    get totalPages() {
        return Math.ceil(this.filteredData.length / this.perPage) || 1;
    },
    
    sort(key) {
        if (this.sortKey === key) {
            this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortKey = key;
            this.sortDir = 'asc';
        }
    },
    
    toggleSelectAll() {
        if (this.selected.length === this.paginatedData.length) {
            this.selected = [];
        } else {
            this.selected = this.paginatedData.map(r => r.id);
        }
    },
    
    toggleSelect(id) {
        const idx = this.selected.indexOf(id);
        if (idx === -1) this.selected.push(id);
        else this.selected.splice(idx, 1);
    },
    
    isSelected(id) {
        return this.selected.includes(id);
    },
    
    nextPage() { if (this.page < this.totalPages) this.page++; },
    prevPage() { if (this.page > 1) this.page--; },
    goPage(p) { this.page = Math.max(1, Math.min(p, this.totalPages)); }
});

// ============================================
// FORM HELPER
// ============================================
window.FormComponent = (initialData = {}, rules = {}) => ({
    data: { ...initialData },
    errors: {},
    touched: {},
    submitting: false,
    
    validate(field = null) {
        const fields = field ? [field] : Object.keys(rules);
        let valid = true;
        
        fields.forEach(f => {
            const value = this.data[f];
            const fieldRules = rules[f];
            if (!fieldRules) return;
            
            this.touched[f] = true;
            
            if (fieldRules.required && (!value || String(value).trim() === '')) {
                this.errors[f] = 'Este campo es obligatorio';
                valid = false;
            } else if (fieldRules.minLength && value && value.length < fieldRules.minLength) {
                this.errors[f] = `Mínimo ${fieldRules.minLength} caracteres`;
                valid = false;
            } else if (fieldRules.email && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                this.errors[f] = 'Email inválido';
                valid = false;
            } else if (fieldRules.pattern && value && !fieldRules.pattern.test(value)) {
                this.errors[f] = fieldRules.message || 'Formato inválido';
                valid = false;
            } else if (fieldRules.custom && !fieldRules.custom(value, this.data)) {
                this.errors[f] = fieldRules.message || 'Valor inválido';
                valid = false;
            } else {
                delete this.errors[f];
            }
        });
        
        return valid;
    },
    
    getFieldError(field) {
        return this.touched[field] && this.errors[field] ? this.errors[field] : null;
    },
    
    hasError(field) {
        return this.touched[field] && !!this.errors[field];
    },
    
    async submit(submitFn) {
        if (!this.validate()) return false;
        this.submitting = true;
        try {
            await submitFn(this.data);
            return true;
        } catch (e) {
            console.error(e);
            return false;
        } finally {
            this.submitting = false;
        }
    },
    
    reset() {
        this.data = { ...initialData };
        this.errors = {};
        this.touched = {};
    }
});

// ============================================
// DROPDOWN / SELECT
// ============================================
window.Dropdown = (options = []) => ({
    open: false,
    selected: null,
    search: '',
    options,
    
    get filteredOptions() {
        if (!this.search) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => 
            String(o.label || o).toLowerCase().includes(q)
        );
    },
    
    select(option) {
        this.selected = option;
        this.open = false;
        this.search = '';
        this.$dispatch('change', { value: option.value ?? option });
    },
    
    toggle() {
        this.open = !this.open;
        if (this.open) this.$nextTick(() => this.$refs.search?.focus());
    },
    
    close() { this.open = false; },
    
    handleKeydown(e) {
        if (e.key === 'Escape') this.close();
        if (e.key === 'Enter' && this.filteredOptions.length === 1) {
            this.select(this.filteredOptions[0]);
        }
    }
});

// ============================================
// TABS COMPONENT
// ============================================
window.TabsComponent = (tabs = []) => ({
    tabs,
    active: tabs[0]?.key || null,
    
    setActive(key) {
        this.active = key;
    }
});

// ============================================
// SIDEBAR COMPONENT
// ============================================
window.SidebarComponent = (items = []) => ({
    collapsed: false,
    items,
    
    toggle() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('sidebarCollapsed', this.collapsed);
    },
    
    init() {
        this.collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    }
});

// ============================================
// KPI CARD COMPONENT
// ============================================
window.KPICard = (value, label, icon, options = {}) => ({
    value: 0,
    displayValue: '0',
    label,
    icon,
    trend: options.trend || null,
    format: options.format || 'number',
    
    init() {
        this.value = value;
        this.animate();
    },
    
    animate() {
        if (window.prefersReducedMotion?.()) {
            this.displayValue = this.formatValue(this.value);
            return;
        }
        
        const obj = { val: 0 };
        gsap.to(obj, {
            val: this.value,
            duration: 1.2,
            ease: 'power1.out',
            onUpdate: () => {
                this.displayValue = this.formatValue(Math.round(obj.val));
            },
            onComplete: () => {
                this.displayValue = this.formatValue(this.value);
            }
        });
    },
    
    formatValue(val) {
        switch (this.format) {
            case 'currency':
                return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(val);
            case 'decimal':
                return val.toFixed(2);
            default:
                return val.toLocaleString('es-ES');
        }
    }
});

// ============================================
// COMMAND PALETTE (Cmd+K)
// ============================================
window.CommandPalette = {
    open: false,
    search: '',
    items: [],
    selectedIndex: 0,
    
    register(items) {
        this.items = items;
    },
    
    toggle() {
        this.open = !this.open;
        this.search = '';
        this.selectedIndex = 0;
        if (this.open) setTimeout(() => document.querySelector('[data-command-search]')?.focus(), 0);
    },
    
    close() { this.open = false; },
    
    get filteredItems() {
        if (!this.search) return this.items.slice(0, 8);
        const q = this.search.toLowerCase();
        return this.items.filter(i => 
            i.label.toLowerCase().includes(q) || 
            (i.description && i.description.toLowerCase().includes(q))
        ).slice(0, 8);
    },
    
    select(item) {
        if (item.action) item.action();
        this.close();
    },
    
    handleKeydown(e) {
        if (e.key === 'Escape') this.close();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredItems.length - 1);
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
        }
        if (e.key === 'Enter' && this.filteredItems[this.selectedIndex]) {
            this.select(this.filteredItems[this.selectedIndex]);
        }
    }
};

// Global Cmd+K listener
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (window.CommandPaletteInstance) {
            window.CommandPaletteInstance.toggle();
        }
    }
});

// ============================================
// CONFIRM DIALOG
// ============================================
window.confirmDialog = (message, options = {}) => {
    return new Promise((resolve) => {
        const { title = 'Confirmar', confirmText = 'Confirmar', cancelText = 'Cancelar', variant = 'danger' } = options;
        
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[90] flex items-center justify-center';
        modal.innerHTML = `
            <div class="fixed inset-0 bg-night-950/70 backdrop-blur-sm" data-modal-backdrop></div>
            <div class="relative w-full max-w-md mx-4 rounded-2xl border border-mist bg-white p-6 shadow-[0_40px_90px_-35px_rgba(2,6,20,0.7)]" data-modal-content>
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-xl ${variant === 'danger' ? 'bg-brand-red/10 text-brand-red' : 'bg-brand-blue-soft text-brand-blue'}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.3 19h15.4a1 1 0 00.9-1.4L13.7 4.6a1 1 0 00-1.7 0L5 17.6A1 1 0 005.9 19z"></path></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-steel-900">${title}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-steel-500">${message}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2.5">
                    <button class="btn btn-secondary py-2" data-cancel>${cancelText}</button>
                    <button class="btn ${variant === 'danger' ? 'btn-danger' : 'btn-primary'} py-2" data-confirm>${confirmText}</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        const cleanup = (result) => {
            document.body.style.overflow = '';
            modal.remove();
            resolve(result);
        };
        
        modal.querySelector('[data-confirm]').onclick = () => cleanup(true);
        modal.querySelector('[data-cancel]').onclick = () => cleanup(false);
        modal.querySelector('[data-modal-backdrop]').onclick = () => cleanup(false);
        
        // Animate
        if (window.prefersReducedMotion?.()) return;
        gsap.fromTo(modal.querySelector('[data-modal-content]'), 
            { scale: 0.95, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.2, ease: 'power2.out' }
        );
        gsap.fromTo(modal.querySelector('[data-modal-backdrop]'), 
            { opacity: 0 }, { opacity: 1, duration: 0.15 }
        );
    });
};

// ============================================
// PREFERS REDUCED MOTION
// ============================================
window.prefersReducedMotion = window.prefersReducedMotion || (() =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches);

// ============================================
// <kpi-card> — tarjeta de indicador usada en el dashboard
// ============================================
const KPI_ICONS = {
    receipt: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
    car: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 19l7-7 3 3-7 7-3-3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 4l-7 7 3 3-7 7-3-3z"></path>',
    droplet: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7-7m0 0l-7 7m7-7v10M5 9l7 7m0 0l7-7m-7 7V5"></path>',
    alert: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>'
};

if (!customElements.get('kpi-card')) {
    customElements.define('kpi-card', class extends HTMLElement {
        static get observedAttributes() {
            return ['value', 'label', 'icon', 'format'];
        }

        connectedCallback() {
            this.render();
        }

        attributeChangedCallback() {
            if (this.isConnected) this.render();
        }

        // Alpine asigna `:value` como propiedad, no como atributo
        get value() {
            return this._value !== undefined ? this._value : this.getAttribute('value');
        }

        set value(raw) {
            this._value = raw;
            if (this.isConnected) this.render();
        }

        formatValue(raw) {
            const value = Number(raw) || 0;
            const format = this.getAttribute('format') || 'number';
            if (format === 'currency') {
                return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
            }
            if (format === 'decimal') return value.toFixed(2);
            return value.toLocaleString('es-ES');
        }

        render() {
            const label = this.getAttribute('label') || '';
            const icon = KPI_ICONS[this.getAttribute('icon')] || KPI_ICONS.receipt;
            const target = Number(this.value) || 0;

            if (!this._root) {
                this.innerHTML = `
                    <div class="kpi-card-inner">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="kpi-label truncate"></p>
                                <p class="kpi-value mt-3" data-kpi-value>0</p>
                                <p class="mt-2 text-[0.62rem] font-semibold uppercase tracking-[0.16em] text-steel-400">Actualizado ahora</p>
                            </div>
                            <span class="kpi-icon">
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
                            </span>
                        </div>
                    </div>`;
                this._root = this.querySelector('[data-kpi-value]');
                this.querySelector('.kpi-label').textContent = label;
            }

            const el = this._root;
            if (window.prefersReducedMotion?.()) {
                el.textContent = this.formatValue(target);
                return;
            }
            gsap.killTweensOf(el);
            const obj = { value: 0 };
            gsap.to(obj, {
                value: target,
                duration: 1.2,
                ease: 'power1.out',
                onUpdate: () => {
                    el.textContent = this.formatValue(obj.value);
                },
                onComplete: () => {
                    el.textContent = this.formatValue(target);
                }
            });
        }
    });
}