# Arquitectura y entorno

## Stack actual
- **Backend:** Laravel 12 (PHP 8.2.4). Rutas y vistas; sin controladores/modelos de dominio todavía.
- **Vistas:** Blade (layouts `main` y `app`).
- **Interactividad:** Alpine.js 3.14 vendorizado en `public/js/vendor/alpine.min.js`.
- **Estilos:** Tailwind CSS 4 compilado con el CLI standalone (`tools/tailwindcss.exe`) desde `resources/css/app.css` hacia `public/css/app.css`. El tema vive en `@theme`. El binario no se versiona (111 MB); ver `tools/README.md`.
- **Animaciones:** GSAP + ScrollTrigger + Flip vendorizados en `public/js/vendor/`.
- **Datos (temporal):** stores JS en `public/js/stores.js` sobre `localStorage`, con datos demo deterministas versionados por `DEMO_VERSION`.
- **Metaheurística (prototipo):** `public/js/metaheuristica.js` expone `window.Metaheuristica` (algoritmo genético de demostración para ordenar vehículos y asignar trabajadores).
- **Diseño:** sistema profesional/futurista documentado en `memoria-ia/diseno.md` (shell oscuro, lienzo claro, GSAP, motivo automotriz).
- **Base de datos:** MySQL/MariaDB de XAMPP (pendiente de configurar; hoy `.env` apunta a SQLite).
- **Build:** ninguno. No hay npm, `package.json`, Vite ni `node_modules`.

## Restricciones verificadas
- Laravel 13 exige PHP ^8.3; el entorno tiene PHP 8.2.4. No actualizar a Laravel 13 sin actualizar PHP.
- El proyecto vive en `C:\xampp\htdocs\multiservicios-las-acacias`; ya está scaffolded y `logo.jpeg` está en `public/`.
- **Alpine debe ser el último script cargado** en los layouts: inicia al evaluarse (`queueMicrotask`) y las expresiones necesitan `window.Stores`, `loginForm`, `Toast`, etc. ya definidos.
- **No hay bundler**: `public/js/*.js` y `public/js/vendor/*.js` son la fuente; no reintroducir `import`/`export` ni `@vite`.
- **Alpine 3 llama `init()` automáticamente**: no usar `x-init="init()"`, se ejecutaría dos veces (rompía animaciones y duplicaba cargas).
- **No hardcodear `opacity: 0`** en estilos Alpine de filas/tarjetas animadas: Alpine reaplica el estilo y deja el contenido invisible (bug corregido en facturación).
- `DEMO_VERSION` en `public/js/stores.js` invalida y regenera los datos demo cuando cambia el set de pruebas.
- **Guardia de sesión demo** en el `<head>` de `layouts/main.blade.php`: si no hay `auth` en `localStorage`, redirige a `/login` antes de renderizar. El menú filtra secciones por rol (`auth.hasRole`); sin sesión solo se veían Dashboard y Mi perfil. El logout (`@submit="auth.logout()"`) limpia el store antes de enviar el formulario.
- `@section('scripts')` de cada vista debe cerrarse con `@endsection` antes de abrir otra sección, o Blade deja `<main>` vacío.
- El docroot de Laravel es `public/`. Apache nunca debe apuntar a la raíz del proyecto porque expondría `.env`.
- `.env` y `.env.example` siguen con SQLite (`.env.example` y `phpunit.xml`). La app debe configurarse para MySQL/MariaDB; los tests siguen usando SQLite `:memory:`.
- Tailwind 4 es CSS-first: no crear `tailwind.config.js`; el tema se define con `@theme` en `resources/css/app.css`. `@source` incluye `resources/views/**/*.blade.php` y `public/js/**/*.js` (las clases usadas desde JS también se generan).
- Paleta de marca (azul, blanco, rojo) definida en `memoria-ia/paleta-colores.md`; fijarla en `@theme` al scaffoldear.
- `config/app.php` fija `'timezone' => 'UTC'`; cambiarlo si las fechas de lavado deben mostrarse en hora local.

## Comandos
Ver `AGENTS.md` para comandos exactos de instalación, desarrollo, tests y formato.
