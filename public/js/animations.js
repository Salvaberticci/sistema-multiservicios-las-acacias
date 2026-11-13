// GSAP Utilities para animaciones consistentes. Requiere gsap/ScrollTrigger/Flip cargados antes.
gsap.registerPlugin(ScrollTrigger, Flip);

// Configuración global
gsap.defaults({
    ease: 'power2.out',
    duration: 0.3
});

// Reducir movimiento si el usuario lo prefiere
const prefersReducedMotion = () => 
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const getDuration = (dur) => prefersReducedMotion() ? 0 : dur;

// ============================================
// PAGE TRANSITIONS
// ============================================
const pageTransitions = {
    // Salida de página
    leave(container, done) {
        if (prefersReducedMotion()) return done();
        
        gsap.to(container, {
            opacity: 0,
            y: -8,
            duration: 0.15,
            ease: 'power2.in',
            onComplete: done
        });
    },
    
    // Entrada de página
    enter(container) {
        if (prefersReducedMotion()) {
            gsap.set(container, { opacity: 1, y: 0 });
            return;
        }
        
        gsap.from(container, {
            opacity: 0,
            y: 8,
            duration: 0.25,
            ease: 'power2.out',
            clearProps: 'all'
        });
    }
};

// ============================================
// MODAL / PANEL ANIMATIONS
// ============================================
const modal = {
    open(modalEl, backdropEl) {
        if (prefersReducedMotion()) {
            gsap.set(modalEl, { scale: 1, opacity: 1 });
            gsap.set(backdropEl, { opacity: 1 });
            return;
        }
        
        gsap.fromTo(modalEl, 
            { scale: 0.95, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.2, ease: 'power2.out' }
        );
        
        gsap.fromTo(backdropEl,
            { opacity: 0 },
            { opacity: 1, duration: 0.15 }
        );
    },
    
    close(modalEl, backdropEl, done) {
        if (prefersReducedMotion()) {
            gsap.set(modalEl, { scale: 0.95, opacity: 0 });
            gsap.set(backdropEl, { opacity: 0 });
            return done?.();
        }
        
        gsap.to(modalEl, {
            scale: 0.95,
            opacity: 0,
            duration: 0.15,
            ease: 'power2.in',
            onComplete: done
        });
        
        gsap.to(backdropEl, { opacity: 0, duration: 0.1 });
    }
};

// ============================================
// SIDEBAR COLLAPSE/EXPAND
// ============================================
const sidebar = {
    toggle(sidebarEl, labelEls, iconEls, collapsed) {
        if (prefersReducedMotion()) {
            gsap.set(sidebarEl, { width: collapsed ? 72 : 280 });
            gsap.set(labelEls, { opacity: collapsed ? 0 : 1 });
            return;
        }
        
        gsap.to(sidebarEl, {
            width: collapsed ? 72 : 280,
            duration: 0.3,
            ease: 'power2.inOut'
        });
        
        gsap.to(labelEls, {
            opacity: collapsed ? 0 : 1,
            duration: 0.15,
            ease: 'power1.inOut'
        });
        
        gsap.to(iconEls, {
            x: 0,
            duration: 0.3,
            ease: 'power2.inOut'
        });
    }
};

// ============================================
// STAGGER REVEAL (listas, tablas, grids)
// ============================================
const staggerReveal = {
    rows(selector, container) {
        if (prefersReducedMotion()) {
            gsap.set(selector, { opacity: 1, y: 0 });
            return;
        }
        
        gsap.from(selector, {
            opacity: 0,
            y: 12,
            duration: 0.3,
            stagger: 0.04,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: container,
                start: 'top 90%',
                once: true
            }
        });
    },
    
    cards(selector, container) {
        if (prefersReducedMotion()) {
            gsap.set(selector, { opacity: 1, scale: 1 });
            return;
        }
        
        gsap.from(selector, {
            opacity: 0,
            scale: 0.95,
            duration: 0.4,
            stagger: 0.06,
            ease: 'back.out(1.5)',
            scrollTrigger: {
                trigger: container,
                start: 'top 85%',
                once: true
            }
        });
    },
    
    list(selector, container) {
        if (prefersReducedMotion()) {
            gsap.set(selector, { opacity: 1, x: 0 });
            return;
        }
        
        gsap.from(selector, {
            opacity: 0,
            x: -20,
            duration: 0.35,
            stagger: 0.05,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: container,
                start: 'top 90%',
                once: true
            }
        });
    }
};

// ============================================
// BUTTON INTERACTIONS
// ============================================
const button = {
    ripple(el, event) {
        if (prefersReducedMotion()) return;
        
        const rect = el.getBoundingClientRect();
        const ripple = document.createElement('span');
        ripple.className = 'btn-ripple';
        ripple.style.cssText = `
            --x: ${event.clientX - rect.left}px;
            --y: ${event.clientY - rect.top}px;
            position: absolute;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.3;
            pointer-events: none;
            width: 100px;
            height: 100px;
            margin-left: -50px;
            margin-top: -50px;
            left: var(--x);
            top: var(--y);
            transform: scale(0);
        `;
        el.style.position = 'relative';
        el.style.overflow = 'hidden';
        el.appendChild(ripple);
        
        gsap.fromTo(ripple,
            { scale: 0, opacity: 0.3 },
            { scale: 4, opacity: 0, duration: 0.4, ease: 'power2.out', onComplete: () => ripple.remove() }
        );
    },
    
    press(el) {
        if (prefersReducedMotion()) return;
        gsap.to(el, { scale: 0.98, duration: 0.1, ease: 'power1.out' });
    },
    
    release(el) {
        if (prefersReducedMotion()) return;
        gsap.to(el, { scale: 1, duration: 0.15, ease: 'back.out(2)' });
    },
    
    loading(spinnerEl) {
        if (prefersReducedMotion()) return;
        gsap.to(spinnerEl, { rotation: 360, duration: 1, ease: 'none', repeat: -1 });
    },
    
    stopLoading(spinnerEl) {
        gsap.killTweensOf(spinnerEl);
        gsap.set(spinnerEl, { rotation: 0 });
    }
};

// ============================================
// FORM VALIDATION ANIMATIONS
// ============================================
const form = {
    error(inputEl, labelEl) {
        if (prefersReducedMotion()) {
            gsap.set(inputEl, { borderColor: '#DD2024' });
            gsap.set(labelEl, { color: '#DD2024' });
            return;
        }
        
        gsap.to(inputEl, {
            x: [-8, 8, -6, 6, -4, 4, 0],
            duration: 0.4,
            ease: 'power1.out'
        });
        
        gsap.to(inputEl, {
            borderColor: '#DD2024',
            duration: 0.1
        });
        
        if (labelEl) {
            gsap.to(labelEl, { color: '#DD2024', duration: 0.1 });
        }
    },
    
    success(inputEl, checkEl) {
        if (prefersReducedMotion()) return;
        
        gsap.to(inputEl, { borderColor: '#22C55E', duration: 0.1 });
        
        if (checkEl) {
            gsap.fromTo(checkEl,
                { scale: 0, rotation: -45 },
                { scale: 1, rotation: 0, duration: 0.3, ease: 'back.out(1.7)' }
            );
        }
    },
    
    clear(inputEl, labelEl) {
        gsap.to(inputEl, { borderColor: '#CCCCCC', duration: 0.1 });
        if (labelEl) gsap.to(labelEl, { color: '#404040', duration: 0.1 });
    }
};

// ============================================
// COUNTER ANIMATION (KPIs)
// ============================================
const counter = {
    animate(el, targetValue, options = {}) {
        const { duration = 1.2, formatter = (v) => v.toLocaleString('es-ES'), ease = 'power1.out' } = options;
        
        if (prefersReducedMotion()) {
            el.textContent = formatter(targetValue);
            return;
        }
        
        const obj = { value: 0 };
        gsap.to(obj, {
            value: targetValue,
            duration,
            ease,
            onUpdate: function() {
                el.textContent = formatter(Math.round(obj.value));
            },
            onComplete: () => {
                el.textContent = formatter(targetValue);
            }
        });
    },
    
    animateDecimal(el, targetValue, decimals = 2, options = {}) {
        const { duration = 1.2, ease = 'power1.out' } = options;
        
        if (prefersReducedMotion()) {
            el.textContent = targetValue.toFixed(decimals);
            return;
        }
        
        const obj = { value: 0 };
        gsap.to(obj, {
            value: targetValue,
            duration,
            ease,
            onUpdate: function() {
                el.textContent = obj.value.toFixed(decimals);
            },
            onComplete: () => {
                el.textContent = targetValue.toFixed(decimals);
            }
        });
    }
};

// ============================================
// TOAST / NOTIFICATION
// ============================================
const toast = {
    show(toastEl) {
        if (prefersReducedMotion()) {
            gsap.set(toastEl, { x: 0, opacity: 1 });
            return;
        }
        
        gsap.fromTo(toastEl,
            { x: 300, opacity: 0 },
            { x: 0, opacity: 1, duration: 0.35, ease: 'power2.out' }
        );
    },
    
    hide(toastEl, done) {
        if (prefersReducedMotion()) {
            gsap.set(toastEl, { x: 300, opacity: 0 });
            return done?.();
        }
        
        gsap.to(toastEl, {
            x: 300,
            opacity: 0,
            duration: 0.25,
            ease: 'power2.in',
            onComplete: done
        });
    }
};

// ============================================
// TABS / STEPPER INDICATOR (Flip)
// ============================================
const tabs = {
    animateIndicator(indicatorEl, targetEl) {
        if (prefersReducedMotion()) {
            gsap.set(indicatorEl, { 
                width: targetEl.offsetWidth,
                left: targetEl.offsetLeft 
            });
            return;
        }
        
        const state = Flip.getState(indicatorEl);
        
        // Move indicator to target position
        gsap.set(indicatorEl, {
            width: targetEl.offsetWidth,
            left: targetEl.offsetLeft
        });
        
        Flip.from(state, {
            duration: 0.3,
            ease: 'power2.inOut',
            absolute: true
        });
    }
};

// ============================================
// DROPDOWN / MENU
// ============================================
const dropdown = {
    open(menuEl, triggerEl) {
        if (prefersReducedMotion()) {
            gsap.set(menuEl, { opacity: 1, scaleY: 1 });
            return;
        }
        
        const rect = triggerEl.getBoundingClientRect();
        gsap.fromTo(menuEl,
            { opacity: 0, scaleY: 0.9, transformOrigin: 'top center' },
            { opacity: 1, scaleY: 1, duration: 0.15, ease: 'power2.out' }
        );
    },
    
    close(menuEl, done) {
        if (prefersReducedMotion()) {
            gsap.set(menuEl, { opacity: 0, scaleY: 0.9 });
            return done?.();
        }
        
        gsap.to(menuEl, {
            opacity: 0,
            scaleY: 0.9,
            transformOrigin: 'top center',
            duration: 0.1,
            ease: 'power2.in',
            onComplete: done
        });
    }
};

// ============================================
// SCROLL TRIGGER REVEAL
// ============================================
const scrollReveal = {
    init(selector, options = {}) {
        if (prefersReducedMotion()) {
            gsap.set(selector, { opacity: 1, y: 0 });
            return;
        }
        
        const { 
            y = 30, 
            opacity = 0, 
            duration = 0.6, 
            stagger = 0.1, 
            start = 'top 85%',
            once = true 
        } = options;
        
        gsap.from(selector, {
            y,
            opacity,
            duration,
            stagger,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: selector,
                start,
                once,
                ...options.scrollTrigger
            }
        });
    }
};

window.GSAP = {
    pageTransitions,
    modal,
    sidebar,
    staggerReveal,
    button,
    form,
    counter,
    toast,
    tabs,
    dropdown,
    scrollReveal
};