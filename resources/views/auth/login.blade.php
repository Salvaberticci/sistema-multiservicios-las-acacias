@extends('layouts.app')

@section('title', 'Iniciar sesión - Multiservicios Las Acacias')

@section('content')
<div class="auth-shell flex min-h-screen flex-col" x-data="loginForm()">
    <div class="auth-grid"></div>

    <!-- Resplandores de fondo -->
    <div class="pointer-events-none absolute -top-40 -left-32 h-[28rem] w-[28rem] rounded-full bg-brand-blue-light/20 blur-[120px] animate-float"></div>
    <div class="pointer-events-none absolute -bottom-48 right-0 h-[30rem] w-[30rem] rounded-full bg-brand-red/15 blur-[130px]"></div>

    <header class="relative z-10 flex items-center justify-between px-6 py-6 lg:px-12">
        <a href="/login" class="flex items-center" title="Multiservicios Las Acacias">
            <span class="brand-tile"><img src="/logo.jpeg" alt="Multiservicios Las Acacias"></span>
        </a>
        <span class="hidden sm:inline-flex chip chip-dark"><span class="status-dot"></span> Plataforma operativa</span>
    </header>

    <div class="relative z-10 flex flex-1 items-center px-6 pb-10 lg:px-12">
        <div class="mx-auto grid w-full max-w-6xl items-center gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">

            <!-- Hero -->
            <section class="hidden lg:block">
                <p class="page-eyebrow text-brand-blue-light">Sistema de gestión automotriz</p>
                <h1 class="mt-4 text-[2.9rem] font-bold leading-[1.05] tracking-[-0.03em] text-white">
                    Tu operación de<br>
                    <span class="text-gradient">lavado y cobro</span><br>
                    bajo control total.
                </h1>
                <p class="mt-5 max-w-md text-[0.95rem] leading-relaxed text-white/55">
                    Facturación, inventario, entradas y salidas de vehículos, comisiones y auditoría forense en un solo panel.
                </p>

                <ul class="mt-7 grid gap-3 text-sm text-white/70">
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-brand-blue-light/12 border border-brand-blue-light/25 text-brand-blue-light">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        Cobros y facturas calculados en el servidor
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-brand-blue-light/12 border border-brand-blue-light/25 text-brand-blue-light">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1m0-12.8l-2.1 2.1m-8.6 8.6l-2.1 2.1"></path><circle cx="12" cy="12" r="3.5"></circle></svg>
                        </span>
                        Tarifas y comisiones por tamaño de vehículo
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg grid place-items-center bg-brand-red/12 border border-brand-red/25 text-brand-red-light">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.6-4A11.9 11.9 0 0112 2.9 11.9 11.9 0 013.4 6 12 12 0 003 9c0 5.6 3.8 10.3 9 11.6 5.2-1.3 9-6 9-11.6 0-1-.1-2.1-.4-3z"></path></svg>
                        </span>
                        Bitácora de auditoría inmutable
                    </li>
                </ul>

                <div class="mt-9 flex items-end gap-6">
                    <div class="flex-1">
                        <svg viewBox="0 0 340 120" fill="none" stroke="rgba(18,178,236,0.55)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 88h18l10-24h38l22-16c8-6 16-8 26-8h30c14 0 25 5 36 14l16 14 28 6c14 3 22 6 22 10" stroke="rgba(18,178,236,0.75)"></path>
                            <path d="M82 64h46l12-18" stroke="rgba(18,178,236,0.5)"></path>
                            <path d="M146 46h40c11 0 19 3 27 11l9 9" stroke="rgba(18,178,236,0.5)"></path>
                            <circle cx="92" cy="88" r="17" stroke="rgba(18,178,236,0.85)"></circle>
                            <circle cx="92" cy="88" r="6" stroke="rgba(18,178,236,0.5)"></circle>
                            <circle cx="268" cy="88" r="17" stroke="rgba(18,178,236,0.85)"></circle>
                            <circle cx="268" cy="88" r="6" stroke="rgba(18,178,236,0.5)"></circle>
                            <path d="M4 88h8m272 0h34" stroke="rgba(18,178,236,0.45)"></path>
                            <path d="M312 54h20M320 42h14M326 30h10" stroke="rgba(221,32,36,0.65)"></path>
                        </svg>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="rounded-xl border border-white/8 bg-white/4 px-4 py-3">
                            <p class="text-xl font-bold text-white">+120</p>
                            <p class="text-[0.6rem] uppercase tracking-[0.16em] text-white/45">Lavados/mes</p>
                        </div>
                        <div class="rounded-xl border border-white/8 bg-white/4 px-4 py-3">
                            <p class="text-xl font-bold text-white">100%</p>
                            <p class="text-[0.6rem] uppercase tracking-[0.16em] text-white/45">Auditado</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tarjeta de acceso -->
            <section class="w-full max-w-md justify-self-center lg:justify-self-end">
                <div x-ref="card" class="auth-card p-7 sm:p-8">
                    <div class="flex items-center gap-3">
                        <span x-ref="logo" class="brand-tile brand-tile--card"><img src="/logo.jpeg" alt="Logo"></span>
                        <div>
                            <p class="text-[0.62rem] uppercase tracking-[0.26em] text-brand-blue-light/70">Acceso seguro</p>
                            <h2 class="text-xl font-semibold text-white">Iniciar sesión</h2>
                        </div>
                    </div>

                    <div class="mt-5 tread-divider"></div>

                    <p class="mt-4 text-sm text-white/50">Ingresa tus credenciales para acceder al panel de operaciones.</p>

                    <p x-show="errors.general" x-text="errors.general" class="mt-4 rounded-lg border border-brand-red/35 bg-brand-red/10 px-3 py-2 text-xs text-brand-red-light"></p>

                    <form @submit.prevent="submit" class="mt-5 space-y-4">
                        <div>
                            <label for="email" class="auth-label">Correo electrónico</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.9 5.3a2 2 0 002.2 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <input type="email" id="email" x-model="form.email" @blur="validate('email')" @input="clearError('email')"
                                       class="auth-input" placeholder="admin@acacias.com" autocomplete="email" required>
                            </div>
                            <p x-show="errors.email" x-text="errors.email" class="mt-1.5 text-xs text-brand-red-light"></p>
                        </div>

                        <div>
                            <label for="password" class="auth-label">Contraseña</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 10-8 0v4M5 11h14v9a1 1 0 01-1 1H6a1 1 0 01-1-1v-9z"></path></svg>
                                <input :type="showPassword ? 'text' : 'password'" id="password" x-model="form.password" @blur="validate('password')" @input="clearError('password')"
                                       class="auth-input pr-11" placeholder="••••••••" autocomplete="current-password" required>
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 grid h-7 w-7 place-items-center rounded-md text-white/40 hover:text-brand-blue-light hover:bg-white/5 transition" aria-label="Mostrar contraseña">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path x-show="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><path x-show="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path x-show="showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.7a3 3 0 004.2 4.2M9.9 5.2A10.6 10.6 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.3 4M6.3 6.3A17 17 0 002 12s3.6 7 10 7a10.6 10.6 0 004.1-.8"></path></svg>
                                </button>
                            </div>
                            <p x-show="errors.password" x-text="errors.password" class="mt-1.5 text-xs text-brand-red-light"></p>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <label class="flex items-center gap-2 text-xs text-white/55">
                                <input type="checkbox" x-model="form.remember" class="auth-check">
                                Recordarme
                            </label>
                            <a href="#" class="text-xs font-medium text-brand-blue-light/85 hover:text-brand-blue-light transition">¿Olvidó su contraseña?</a>
                        </div>

                        <button type="submit" :disabled="loading" class="btn btn-primary w-full py-3">
                            <span x-show="!loading" class="flex items-center justify-center gap-2">
                                Entrar al sistema
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6l6 6-6 6"></path></svg>
                            </span>
                            <span x-show="loading" class="flex items-center justify-center gap-2" style="display: none;">
                                <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.6a8 8 0 1011.8-3.6M20 20v-5h-.6"></path></svg>
                                Accediendo…
                            </span>
                        </button>
                    </form>

                    <div x-ref="demo" class="mt-6 rounded-xl border border-white/8 bg-white/4 p-3">
                        <p class="text-[0.6rem] font-semibold uppercase tracking-[0.18em] text-white/40">Accesos demo</p>
                        <div class="mt-2 grid grid-cols-2 gap-1.5">
                            <template x-for="cred in [
                                { role: 'Admin', email: 'admin@acacias.com', password: 'admin123' },
                                { role: 'Operador', email: 'operador@acacias.com', password: 'operador123' },
                                { role: 'Cajero', email: 'cajero@acacias.com', password: 'cajero123' },
                                { role: 'Auditor', email: 'auditor@acacias.com', password: 'auditor123' }
                            ]" :key="cred.role">
                                <button type="button" @click="fillDemo(cred)" class="rounded-lg border border-white/8 px-2.5 py-2 text-left transition hover:border-brand-blue-light/40 hover:bg-brand-blue-light/8">
                                    <span class="block text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-brand-blue-light/80" x-text="cred.role"></span>
                                    <span class="block truncate text-[0.65rem] text-white/45" x-text="cred.email"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <p class="mt-5 text-center text-[0.65rem] uppercase tracking-[0.2em] text-white/25">© 2026 Multiservicios Las Acacias</p>
            </section>
        </div>
    </div>
</div>
@endsection
