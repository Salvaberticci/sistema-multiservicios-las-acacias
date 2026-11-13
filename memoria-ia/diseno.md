# Diseño de interfaz (profesional / futurista)

Rediseño completo del frontend hecho el **2026-11-13**, sin npm/Vite. Todo el estilo vive en
`resources/css/app.css` (fuente) y se compila a `public/css/app.css` con el CLI standalone.

## Principios
- **Marca primero:** solo azul `#263F9B`, cian `#12B2EC`, rojo `#DD2024` y blanco, más neutros fríos.
- **Shell oscuro + lienzo claro:** sidebar y topbar en azul noche con glassmorphism; el área de trabajo
  se mantiene clara para no romper la legibilidad de las vistas existentes (`text-gray-900`, `bg-white`).
- **Motivo automotriz sutil:** silueta de auto, líneas de carrocería y rejilla técnica como marca de agua;
  nunca como ilustración dominante.
- **Profesional antes que espectacular:** glow, degradados y grid con baja opacidad; el color rojo se
  reserva para alertas, acciones destructivas y detalles de marca.

## Tipografía
- **Space Grotesk** (variable, 400–700) vendorizada en `public/fonts/space-grotesk-latin.woff2` y
  `...-latin-ext.woff2`; se declara con `@font-face` (sin CDN).
- Titulares con `letter-spacing` negativo; etiquetas de UI en mayúsculas pequeñas con tracking amplio.

## Tokens (`@theme` en `resources/css/app.css`)
| Grupo | Tokens |
|---|---|
| Marca | `brand-blue`, `brand-blue-dark`, `brand-blue-light`, `brand-blue-soft`, `brand-red`, `brand-red-dark`, `brand-red-light`, `brand-red-soft` |
| Noche (shell) | `night-950 … night-600` (`#04081a` → `#1a2b5e`) |
| Neutros fríos | `ink`, `steel-900/700/500/400`, `ice` (`#f4f7ff`), `mist` |
| Sombras | `shadow-card`, `shadow-lift`, `shadow-glow-blue`, `shadow-glow-red` |
| Radios | `radius-xl2` |

## Shell (`layouts/main.blade.php`)
- `.app-shell` — fondo con rejilla técnica + halos azul/rojo; `isolation: isolate`.
- `.app-sidebar` — glass oscuro, marca con logo, nav agrupado **Operación** (Dashboard, Facturación,
  Inventario, Vehículos, Lavados) y **Control** (Auditoría, Seguridad, Mi perfil, Ajustes), colapso real
  a 88 px, panel de estado "Sistema en línea" y tarjeta de usuario con salir.
- **Logo:** `logo.jpeg` es un banner de 1009×360, así que `.brand-tile` es una placa horizontal
  (9.75×3 rem) con `object-fit: contain`: se ve completo, sin recortes, en menú y login. Al colapsar el
  menú la placa pasa a cuadrado y muestra el monograma "A" (`.is-collapsed .brand-tile::after`); la
  tarjeta de login usa `.brand-tile--card` (7.25×2.3 rem) para no empujar el título.
  En móvil entra desde la izquierda con `.is-open` y backdrop dentro del shell (por el stacking context).
- `.app-topbar` — mismo degradado navy que el sidebar (sin franja roja superior ni línea inferior de
  colores), breadcrumbs, chip "EN VIVO", buscador con `⌘K`, notificaciones y menú de usuario.
- `.app-main` — lienzo claro con `.page-shell` (máx. 1500 px) y marca de agua de auto.
- Command palette global (`⌘/Ctrl+K`) con acciones rápidas.

## Componentes
- `.surface`, `.surface-flat`, `.surface-hero` (hero oscuro con rejilla, halo y silueta de auto).
- `.btn-primary` (degradado azul→cian con glow), `.btn-secondary`, `.btn-ghost`, `.btn-danger`.
- `.chip` + `.chip-blue/-dark/-green/-red` para estados.
- `<kpi-card>` (custom element en `public/js/components.js`): `.kpi-card-inner`, `.kpi-label`,
  `.kpi-value`, `.kpi-icon`, con contador animado por GSAP.
- Login (`auth/login.blade.php`): `.auth-shell`, `.auth-grid`, `.auth-card` (glass), `.auth-input`,
  `.auth-label`, `.auth-check` (checkbox propio), hero con titular, bullets, silueta de auto + stats y
  accesos demo.
- Dashboard: hero con saludo, gauge de meta diaria, 4 KPIs, gráfico de canvas con degradado y curva
  suavizada, accesos rápidos, vehículos dentro y actividad reciente.
- Empleados: listado con KPIs por turno, filtros, alta y edición; avatar con iniciales y estado activo/inactivo.
- Planificación (`/vehiculos/planificacion` y `/lavados/planificacion`): KPIs, aviso de demostración,
  parámetros del algoritmo genético, tabla de orden/asignación y panel de carga por trabajador.
- Toasts y `confirmDialog` rediseñados en glass oscuro.

## Animaciones
- GSAP + ScrollTrigger + Flip vendorizados (`public/js/vendor/`).
- Entrada del shell en `public/js/app.js`: sidebar, nav, topbar y `.page-shell` (respeta
  `prefers-reduced-motion`).
- Cada vista conserva su `animateEntrance()` (toolbar, tarjetas, filas).
- Contadores KPI animados, gauge con trazo SVG, gráfico dibujado a mano con DPR para nitidez.
- Keyframes CSS de apoyo: `float-slow`, `shine`, `ping-soft`.
- Regla: si se usa `gsap.from` sobre filas de tabla, **no** dejar `:style="{ opacity: 0 }"` en la
  plantilla (Alpine lo reaplica y las filas quedan invisibles). Fue el bug corregido en facturación.

## Datos demo
- Viven en `localStorage` (`public/js/stores.js`) con PRNG determinista (`Demo`), de modo que todos los
  navegadores ven la misma actividad, incluida "hoy".
- `DEMO_VERSION` (constante en `stores.js`) versiona el set: al cambiarla se borran y regeneran
  `facturas`, `clientes`, `vehiculos`, `lavados`, `inventario` y `auditoria`.
- Credenciales demo: `admin@acacias.com`, `operador@acacias.com`, `cajero@acacias.com`,
  `auditor@acacias.com`, todas con contraseña `admin123`.

## Archivos clave
- `resources/css/app.css` — tema, componentes, utilidades, keyframes.
- `public/css/app.css` — compilado; recompilar tras cambiar clases:
  `./tools/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify`.
- `resources/views/layouts/{main,app}.blade.php`, `auth/login.blade.php`, `dashboard.blade.php`.
- `public/js/{app,stores,components,animations}.js` (scripts globales, sin módulos).

## Verificación hecha
- Chrome headless vía CDP: login real, dashboard (KPIs 8/10/13/2), listados, creates, shows y
  configuración; 0 excepciones, 0 errores de consola, sin scroll horizontal en 1440 px y 390 px.
- Capturas revisadas de login, dashboard, facturación, lavados, inventario, vehículos, auditoría,
  seguridad y menú móvil.
