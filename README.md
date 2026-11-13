# Multiservicios Las Acacias

Sistema de gestión para **Multiservicios Las Acacias**: cobro y facturación, inventario, entrada/salida de vehículos, lavados y comisiones, auditoría y seguridad.

> **Estado actual:** demo funcional de frontend. Los datos se guardan en `localStorage` y el inicio de sesión es simulado. No hay controladores, modelos ni migraciones de dominio; la base de datos solo contiene las tablas base de Laravel. El contexto completo está en `memoria-ia/`.

## Inicio rápido en Windows

1. Haz doble clic en `iniciar.bat` (o ejecuta `.\iniciar.bat` desde PowerShell).
2. El script instala dependencias, crea `.env`, genera la clave, prepara SQLite, aplica migraciones, inicia el servidor y abre el navegador.
3. Inicia sesión con `admin@acacias.com` / `admin123`.

La primera vez puede tardar por `composer install` si la carpeta no incluye `vendor`. Si recibiste la carpeta completa con `vendor`, no necesitas Composer. Si usas MySQL, configura `.env` antes de ejecutar el script; si no, usará SQLite. Para detener el servidor, cierra la ventana que se abre con el servidor o pulsa `Ctrl+C` en ella.

Si el navegador no se abre automáticamente, entra manualmente a la URL que muestra la ventana del script (por ejemplo, `http://127.0.0.1:8000`).

Si prefieres hacerlo manualmente, sigue los pasos de abajo.

## Stack

- **Laravel 12** / PHP 8.2+
- **Blade** + **Tailwind CSS 4** (CSS compilado, sin npm/Vite)
- **Alpine.js**, **GSAP**, **ScrollTrigger** y **Flip** vendorizados localmente
- **SQLite** para desarrollo rápido o **MySQL/MariaDB** con XAMPP/phpMyAdmin
- **Sin Node.js** en el flujo normal

## Requisitos

- PHP 8.2 o superior
- Composer 2
- Git
- MySQL/MariaDB (opcional; SQLite funciona sin servidor)
- No se necesita Node.js ni npm

## Instalación local

### 1. Clonar el repositorio

```bash
git clone https://github.com/Salvaberticci/sistema-multiservicios-las-acacias.git
cd sistema-multiservicios-las-acacias
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Crear el archivo de entorno

En PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

En bash:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos

#### Opción A: SQLite (recomendada para probar rápido)

El `.env.example` ya usa SQLite. Crear el archivo y migrar:

```powershell
New-Item database/database.sqlite -ItemType File -Force
php artisan migrate --seed
```

En bash:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

#### Opción B: MySQL/MariaDB con XAMPP

1. Iniciar **MySQL** desde el panel de XAMPP.
2. Crear la base `multiservicios_las_acacias` en phpMyAdmin (`http://localhost/phpmyadmin`).
3. Editar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multiservicios_las_acacias
DB_USERNAME=root
DB_PASSWORD=
```

4. Migrar:

```bash
php artisan migrate --seed
```

> Ajustar usuario y contraseña según la configuración local. No commitear `.env`.

### 5. Ejecutar la aplicación

```bash
composer dev
```

o directamente:

```bash
php artisan serve
```

Abrir `http://127.0.0.1:8000`.

#### Con Apache/XAMPP

Configurar el docroot en `public/`, no en la raíz del proyecto. La URL sería:

`http://localhost/multiservicios-las-acacias/public`

No apuntar Apache a la raíz: expondría `.env`. Si se usa Apache, `APP_URL` en `.env` debe coincidir con esa URL.

## Credenciales demo

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@acacias.com` | `admin123` |
| Operador | `operador@acacias.com` | `admin123` |
| Cajero | `cajero@acacias.com` | `admin123` |
| Auditor | `auditor@acacias.com` | `admin123` |

## Datos demo

- Se guardan en `localStorage` del navegador.
- Para regenerarlos, cambiar `DEMO_VERSION` en `public/js/stores.js` y recargar.
- Para limpiar todo, borrar los datos del sitio desde DevTools → Application → Local Storage.

## Recompilar CSS

`public/css/app.css` ya está compilado y versionado. Si cambias clases de Tailwind en Blade o en JS, recompila:

```powershell
.\tools\tailwindcss.exe -i resources\css\app.css -o public\css\app.css --minify
```

En bash:

```bash
./tools/tailwindcss.exe -i resources/css/app.css -o public/css/app.css --minify
```

El binario `tools/tailwindcss.exe` **no se incluye en el repositorio** porque pesa ~111 MB (supera el límite de GitHub). Descargar el CLI standalone para Windows desde [Tailwind CSS Releases](https://github.com/tailwindlabs/tailwindcss/releases/latest) (`tailwindcss-windows-x64.exe`) y guardarlo como `tools/tailwindcss.exe`.

`resources/css/app.css` incluye `@source` para las vistas y para `public/js/**/*.js`, así que las clases usadas desde JavaScript también se generan.

## Tests

```bash
php artisan test
php artisan test --filter=NombreDelTest
```

La suite usa SQLite en memoria (`phpunit.xml`); no necesita MySQL.

## Estructura

- `routes/web.php` — rutas que devuelven vistas Blade; autenticación simulada.
- `resources/views/` — vistas por módulo (dashboard, facturación, inventario, vehículos, lavados, auditoría, seguridad).
- `resources/css/app.css` — fuente de Tailwind (tema, tokens y componentes).
- `public/css/app.css` — CSS compilado; se versiona.
- `public/js/stores.js` — datos demo y persistencia en `localStorage`.
- `public/js/components.js` — componentes Alpine.
- `public/js/animations.js` — animaciones GSAP.
- `public/js/app.js` — inicialización general.
- `public/js/vendor/` — GSAP, ScrollTrigger, Flip y Alpine locales.
- `memoria-ia/` — contexto, decisiones y bitácora para futuras sesiones.

## Problemas comunes

- **Error 500 o “No application encryption key”**: falta `.env` o `APP_KEY`. Ejecutar `php artisan key:generate` y `php artisan config:clear`.
- **No se ven estilos nuevos**: recompilar CSS y ejecutar `php artisan view:clear`.
- **Alpine no responde**: verificar que `alpine.min.js` se cargue al final, después de stores, componentes, animaciones y app (ver `resources/views/layouts/partials/scripts.blade.php`).
- **SQLite “database does not exist”**: crear `database/database.sqlite` antes de migrar.
- **Puerto 8000 ocupado**: `php artisan serve --port=8001`.

## Notas de seguridad

- `.env` está ignorado por git; nunca commitear credenciales.
- Las credenciales demo son públicas y el login es simulado; esta versión no debe usarse como producción.
- La auditoría y la seguridad reales requieren backend, base de datos y permisos por rol (pendiente).

## Repositorio

https://github.com/Salvaberticci/sistema-multiservicios-las-acacias
