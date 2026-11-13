# AGENTS.md

## Contexto
- **Multiservicios Las Acacias**: sistema de cobro/facturación, inventario, entrada/salida de vehículos, comisiones por lavado, auditoría forense y seguridad.
- UI, mensajes y etiquetas de negocio en español. Términos del dominio: vehículo, tamaño (pequeño/mediano/grande), lavado, comisión, factura, auditoría.
- **Estado actual:** Laravel 12 scaffolded con frontend completo (Blade + Tailwind 4 + Alpine + GSAP), **sin npm/Vite/build de JS** y **sin backend de dominio**: los datos viven en stores JS sobre `localStorage` (datos demo). No hay controladores, modelos ni migraciones de negocio; el login es simulado.

## Memoria del proyecto
- Leer `memoria-ia/README.md` antes de trabajar; ahí está el índice de contexto, módulos, arquitectura, diseño, decisiones y progreso.
- `memoria-ia/diseno.md` documenta el sistema visual (tokens, shell, componentes, animaciones y datos demo).
- Mantener la memoria al día: registrar cambios relevantes en `memoria-ia/progreso.md` y decisiones nuevas o pendientes en `memoria-ia/decisiones.md`.
- Si la memoria contradice el código o la configuración, gana el código; corregir la memoria.

## Entorno (Windows + XAMPP)
- PHP 8.2.4, Composer 2.8.9, Node 22 (solo para herramientas puntuales; el frontend **no** usa Node).
- Usar el PHP de `C:\xampp\php`.
- **Laravel 12 fijado** (`laravel/laravel:^12.0`): Laravel 13 exige PHP ^8.3 y el entorno tiene 8.2.4.
- MySQL/MariaDB de XAMPP 10.4.28 (`C:\xampp\mysql\bin\mysql.exe`). Iniciar MySQL desde XAMPP antes de migrar. Apache solo hace falta si se sirve por `http://localhost/...`; `composer dev` usa `php artisan serve`.
- **Nunca apuntar Apache a la raíz del proyecto**: expondría `.env`. El docroot es `public/`; con Apache la URL sería `http://localhost/multiservicios-las-acacias/public` y `APP_URL` debe coincidir.
- Shell: PowerShell (o bash). No hay `make` ni scripts de tarea propios.

## Comandos
- `composer dev`: solo `php artisan serve` (no incluye Vite ni colas; no hay frontend que compilar).
- `php artisan migrate --seed`: migraciones (SQLite por defecto; configurar `.env` para MySQL).
- `php artisan test` / `php artisan test --filter=NombreDelTest`: suite PHPUnit (SQLite `:memory:`).
- `php vendor/bin/pint`: formato PHP (Pint incluido en Laravel 12).
- **Recompilar CSS** tras añadir/cambiar clases:
  `./tools/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify`
  (CLI standalone en `tools/`, sin npm). El `.exe` **no se versiona** (111 MB); descargarlo desde Tailwind Releases y guardarlo ahí. Ver `tools/README.md`.
- `php artisan view:clear` si una vista no refleja cambios.
- Crear la BD en phpMyAdmin (`http://localhost/phpmyadmin`) y ajustar credenciales en `.env`; no commitear `.env`.

## Frontend sin npm
- **Prohibido** reintroducir `package.json`, `node_modules`, Vite, `@vite`, o imports ES en `public/js`.
- Assets locales: `/css/app.css`, `/js/*.js`, `/js/vendor/*.js` (GSAP, ScrollTrigger, Flip, Alpine) y `/fonts/*.woff2`. Sin CDN.
- **JS como scripts globales** en `public/js/`: `stores.js`, `components.js`, `animations.js`, `app.js`. Sin `import`/`export`.
- **Alpine debe cargarse al final** (después de stores/componentes/app) o las expresiones fallan.
- Al **añadir clases desde JS**, recordar que `@source` incluye `public/js/**/*.js`; recompilar CSS.
- No duplicar la inicialización: Alpine 3 llama `init()` automáticamente; **no añadir `x-init="init()"`** en vistas con método `init()`.
- No dejar estilos inline de entrada (`:style="{ opacity: 0 }"`) en filas/tarjetas animadas: Alpine los reaplica y el contenido queda invisible.

## Dominio y reglas
- Módulos: facturación/cobros, inventario, entradas/salidas de vehículos, lavados/comisiones, auditoría y seguridad/usuarios.
- Cada lavado registra vehículo, tamaño, empleado, fecha/hora, precio y comisión; el historial debe mostrar la fecha del lavado.
- El tamaño (pequeño/mediano/grande) determina la tarifa.
- Tarifas, totales y comisiones se calculan en el servidor; no confiar en valores del cliente (hoy el demo calcula en JS porque no hay backend).
- Auditoría forense: bitácora persistida en BD (no solo `storage/logs`), inmutable desde la UI, con usuario, acción, entidad y timestamp. No borrar físicamente registros financieros/de auditoría; usar borrado lógico o anulación.
- Seguridad: autenticación y permisos por rol.
- Sin definir (no asumir): nombre de BD; roles y alcance de seguridad; numeración/PDF de facturas; métodos de pago; fórmula de comisión.

## Estilos, animaciones y fechas
- Tailwind 4 CSS-first: **no crear `tailwind.config.js`**; el tema va con `@theme` en `resources/css/app.css`.
- Paleta obligatoria: azul `#263F9B`, azul claro `#12B2EC`, rojo `#DD2024`, blanco `#FFFFFF`, más neutros fríos. Ver `memoria-ia/diseno.md` y `paleta-colores.md`.
- Shell oscuro (navy glass) + lienzo de contenido claro; motivo automotriz sutil. No introducir Bootstrap ni jQuery.
- GSAP/ScrollTrigger/Flip vendorizados; no hace falta importarlos: están como globales (`window.gsap`, `window.ScrollTrigger`). Respetar `prefers-reduced-motion`.
- Datos demo deterministas en `localStorage` (`public/js/stores.js`). `DEMO_VERSION` fuerza la regeneración al cambiarla.
- Credenciales demo: `admin@acacias.com`, `operador@acacias.com`, `cajero@acacias.com`, `auditor@acacias.com`; contraseña `admin123`.
- `config/app.php` fija `'timezone' => 'UTC'`; si las fechas de lavado deben verse en hora local, cambiarlo ahí.

## Tests
- `phpunit.xml` usa SQLite `:memory:`; la suite no necesita MySQL. Evitar SQL crudo específico de MySQL o cubrirlo con tests.
