# Progreso

Bitácora de sesiones. Añadir una entrada al terminar cambios relevantes.

## 2026-11-12
- Creado `AGENTS.md` con entorno, comandos y reglas del proyecto.
- Creada `memoria-ia/` con contexto, módulos, arquitectura, decisiones y esta bitácora.
- **Estado:** no hay aplicación Laravel scaffolded todavía. Solo existe `logo.jpeg` y documentación.
- **Siguiente:** scaffoldear Laravel 12 en carpeta temporal, fusionar en la raíz y mover `logo.jpeg` a `public/`.
- Definida la paleta de colores del sistema a partir del logo (azul `#263F9B`, azul claro `#12B2EC`, rojo `#DD2024`, blanco `#FFFFFF`) en `memoria-ia/paleta-colores.md`.
- Scaffold Laravel 12 completado: dependencias PHP/JS instaladas, Tailwind 4 + GSAP configurados, paleta en `@theme`, `logo.jpeg` en `public/`, build OK, servidor en `http://127.0.0.1:8000`.

## 2026-11-13
- **Frontend convertido a "sin npm / sin Vite / sin build"** por petición del usuario:
  - Eliminados `package.json`, `package-lock.json`, `vite.config.js`, `node_modules/` y `public/build/`.
  - Tailwind 4 compilado con CLI standalone `tools/tailwindcss.exe` → `public/css/app.css` (61 KB).
  - GSAP, ScrollTrigger, Flip y Alpine 3.14 vendorizados en `public/js/vendor/`.
  - `resources/js/*` reescrito a scripts globales en `public/js/{stores,components,animations,app}.js` (sin `import`/`export`).
  - Layouts sin `@vite`; assets por `/css/app.css` y `/js/*`; `@stack('scripts')` → `@yield('scripts')`; eliminado Ziggy/`route()`.
- **Bugs corregidos para que la UI arranque:**
  - Alpine cargado al final (antes iniciaba antes de definir `loginForm`, stores y componentes).
  - `initStores()` sí se ejecuta; sesión de `localStorage` se restaura.
  - Implementado el custom element `<kpi-card>` (dashboard mostraba tarjetas vacías); soporta binding por propiedad de Alpine.
  - `<canvas>` real para el gráfico del dashboard; `window.utils` global; breadcrumb sin claves duplicadas.
  - Vistas `inventario`, `facturacion/create`, `lavados/create` y `auditoria` sin errores de propiedades nulas/getters duplicados; paginación de auditoría implementada.
  - **9 vistas tenían `@section('scripts')` sin `@endsection` previo** (lavados/create, auditoria/index, seguridad/index, ajustes, perfil, auditoria/show, inventario/edit, lavados/edit, lavados/show, seguridad/create, seguridad/edit, vehiculos/show) → `<main>` vacío. Corregido.
  - `vehiculos/index` usaba `{{ stats.dentro }}` (Blade) sobre datos Alpine → 500. Cambiado a `x-text`.
- **Verificación:** `php artisan serve` + Chrome headless vía CDP sobre 23 rutas (listados, creates, shows y edits): 0 excepciones y 0 errores de consola; login demo redirige a `/dashboard`, KPIs con datos seed y menú lateral renderizado.
- **Pendiente:** backend de dominio (controladores, modelos, migraciones), auth real, sustituir stores de `localStorage`, y decisión de estrategia CSS/vendor para producción.

## 2026-11-13 — Rediseño profesional/futurista + datos demo
- **Rediseño visual completo** (paleta azul/cian/rojo/blanco, motivo automotriz sutil):
  - `resources/css/app.css` reescrito con tokens de marca, noche, neutros fríos, sombras/glow, tipografía
    Space Grotesk local y componentes (`.surface-hero`, `.btn-*`, `.chip*`, `.kpi-*`, `.auth-*`, shell).
  - `layouts/main.blade.php` nuevo: sidebar glass con grupos Operación/Control, colapso, estado del sistema,
    topbar con breadcrumbs, buscador `⌘K`, notificaciones y usuario; backdrop móvil dentro del shell.
  - `auth/login.blade.php` nuevo: hero cinematográfico con silueta de auto, bullets, stats, tarjeta glass y
    accesos demo; conserva bindings y refs del componente `loginForm`.
  - `dashboard.blade.php` nuevo: hero con gauge de meta diaria, 4 KPIs, gráfico con curva suavizada y
    degradado (canvas con DPR), accesos rápidos, vehículos dentro y actividad reciente.
  - Toasts, `confirmDialog` y `<kpi-card>` reestilizados; entradas animadas del shell en `public/js/app.js`.
- **Datos demo de prueba** (`public/js/stores.js`): PRNG determinista y nuevo set de facturas, clientes,
  vehículos, lavados, inventario y auditoría, con movimiento "hoy". `DEMO_VERSION` fuerza la regeneración.
- **Bugs encontrados y corregidos durante la verificación:**
  - Facturación renderizaba 64 filas ignorando la paginación y todas invisibles por `:style="{ opacity: 0 }"`
    fijo en la plantilla; ahora usa `paginatedFacturas` y las filas se ven.
  - 25 vistas con `x-init="init()"` duplicaban `init()` (Alpine ya lo llama): eliminado en todas.
  - Icono de ojo del detalle de lavado mal formado y acciones que se partían en dos líneas; corregidos.
  - Título del detalle de lavado mostraba `#L-lav-de`; ahora muestra la placa real.
  - Backdrop móvil quedaba encima del sidebar por el stacking context de `.app-shell`; movido dentro del shell.
- **Verificación:** Chrome headless vía CDP con login real (`admin@acacias.com` / `admin123`) sobre 15 rutas
  + 4 detalles: 0 excepciones y 0 errores de consola, sin overflow horizontal en 1440 px ni 390 px; menú
  móvil abre/cierra (‑280 px → 0). KPIs del dashboard: 8 facturas, 10 vehículos dentro, 13 lavados, 2 stock bajo.
  Capturas revisadas de login, dashboard, facturación, lavados, inventario, vehículos, auditoría, seguridad y móvil.
- **Logo corregido:** el archivo es un banner 1009×360 y se recortaba con `object-fit: cover` en un
  cuadro de 46 px. Ahora usa una placa horizontal con `contain` (menú y login), monograma "A" al colapsar
  el menú y variante compacta en la tarjeta de login.
- **Topbar unificada:** se quitó la franja roja superior y la línea degradada inferior; `.app-topbar` usa
  el mismo fondo (radiales azul/rojo + degradado navy) que `.app-sidebar`.
- **Menú incompleto corregido:** el filtro por rol dejaba solo Dashboard y Mi perfil cuando no había sesión
  (el navegador mostraba "Invitado"). Añadida guardia en el layout que redirige a `/login` sin sesión y
  logout que limpia `localStorage`; con admin verificado que aparecen las 9 secciones.
- **Documentación:** creada `memoria-ia/diseno.md`; actualizados `README.md`, `arquitectura.md`,
  `decisiones.md`, `progreso.md` y `AGENTS.md` al estado real (sin npm, diseño y datos demo).
- **Pendiente:** backend de dominio, autenticación real y migrar los stores a peticiones al servidor.

## 2026-11-13 — Guía local y publicación
- Reemplazado el README genérico de Laravel por una guía de instalación y ejecución local (SQLite y MySQL/XAMPP, Apache, CSS, tests y credenciales demo).
- Añadido `.gitignore` para excluir `vendor/`, `.env`, logs, SQLite y el binario de Tailwind.
- Añadido `tools/README.md` con instrucciones para descargar el CLI standalone.
- Actualizado `.env.example`: nombre del proyecto, locale español, `APP_URL` y bloque comentado de MySQL.
- Corregido el test de `/` para esperar la redirección a login; la suite pasa (3 tests).
- Publicado en GitHub: `https://github.com/Salvaberticci/sistema-multiservicios-las-acacias` (rama `main`, commit inicial `34c1cf0`).

## 2026-11-13 — Script de arranque local
- Añadidos `iniciar.bat` e `iniciar.ps1`: instalan dependencias si faltan, crean `.env`, generan `APP_KEY`, preparan SQLite, aplican migraciones, buscan un puerto libre, inician `php artisan serve` y abren el navegador.
- Probado en modo `-NoServe` y con servidor real (`-NoBrowser`): `/login` respondió HTTP 200; el proceso PHP se detuvo después de la prueba.
- `iniciar.bat` probado con `-NoServe`; el wrapper reenvía argumentos al `.ps1`.
- Ajustado `iniciar.ps1`: Composer solo se exige si falta `vendor`; probado de nuevo sin Composer.
- README actualizado con la sección "Inicio rápido en Windows".
- **Pendiente:** probar la apertura automática del navegador en una sesión de escritorio (el resto del flujo ya se verificó).

## 2026-11-13 — Metaheurística definida
- Registrada la metaheurística del proyecto: **algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles**.
- Documentada en `memoria-ia/metaheuristica.md`; aún no implementada.
- **Pendiente:** definir función de aptitud, restricciones, parámetros genéticos y ubicación en la interfaz.

## Plantilla

```markdown
## AAAA-MM-DD
- Cambios:
- Verificación:
- Pendientes:
```
