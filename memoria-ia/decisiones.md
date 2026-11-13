# Decisiones

## Tomadas
| Fecha | Decisión | Motivo |
|---|---|---|
| 2026-11-12 | Usar Laravel 12, no 13 | Laravel 13 exige PHP ^8.3 y el entorno tiene PHP 8.2.4 |
| 2026-11-12 | ~~Tailwind CSS 4 vía `@tailwindcss/vite`~~ (sustituida 2026-11-13) | Era lo que integraba el esqueleto de Laravel 12 |
| 2026-11-12 | MySQL/MariaDB de XAMPP + phpMyAdmin | Entorno local definido por el usuario |
| 2026-11-12 | ~~GSAP por npm/Vite~~ (sustituida 2026-11-13) | Evitaba CDN, pero el usuario pidió prescindir de npm |
| 2026-11-13 | Frontend sin npm, sin Vite y sin build de JS | El usuario quiere editar Blade/CSS/JS y recargar, sin toolchain Node ni proceso de desarrollo |
| 2026-11-13 | Tailwind compilado con el CLI standalone (`tools/tailwindcss.exe`) a `public/css/app.css` | Mantiene `@theme` y clases arbitrarias sin npm; solo se recompila al cambiar clases |
| 2026-11-13 | GSAP + Alpine vendorizados en `public/js/vendor/` | Cero CDN y cero npm; Alpine debe cargarse después de los scripts de la app |
| 2026-11-13 | Datos demo en `localStorage` (`public/js/stores.js`) hasta que exista backend | Permite desarrollar el frontend completo primero, como pidió el usuario |
| 2026-11-13 | Datos demo deterministas con PRNG propio y `DEMO_VERSION` | Todos los navegadores ven la misma actividad (incluida "hoy") y el set se regenera al cambiarlo |
| 2026-11-13 | Shell oscuro (navy glass) + lienzo de contenido claro | Look profesional/futurista sin romper la legibilidad de las vistas existentes (`bg-white`, `text-gray-900`) |
| 2026-11-13 | Tipografía Space Grotesk vendorizada en `public/fonts/` | Identidad técnica sin CDN ni npm |
| 2026-11-13 | Quitar `x-init="init()"` de todas las vistas | Alpine 3 ya invoca `init()`; duplicaba inicialización y animaciones |
| 2026-11-13 | Guardia de sesión en el layout principal + logout que limpia el store | Sin sesión el menú solo mostraba Dashboard y Mi perfil; ahora redirige a `/login` y el logout no deja sesión fantasma |
| 2026-11-12 | UI y textos de negocio en español | Idioma del usuario y del dominio |
| 2026-11-12 | Auditoría en base de datos, no solo logs | Requisito de auditoría forense |
| 2026-11-12 | Paleta de colores: azul, blanco y rojo (tomados del logo) | Identidad visual debe coincidir con `logo.jpeg`; detalle en `paleta-colores.md` |
| 2026-11-13 | Publicar en GitHub: `Salvaberticci/sistema-multiservicios-las-acacias` | Compartir el proyecto y permitir clonarlo |
| 2026-11-13 | No versionar `tools/tailwindcss.exe` | Pesa ~111 MB y supera el límite de GitHub; se documenta su descarga |
| 2026-11-13 | Script de arranque local `iniciar.bat` + `iniciar.ps1` | Automatiza instalación, `.env`, SQLite, migraciones, servidor y apertura del navegador |
| 2026-11-13 | Metaheurística: algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles | Definido por el usuario/profesora; aún no implementado |

## Abiertas (no asumir)
- Nombre de la base de datos.
- Roles y alcance del módulo de seguridad (2FA, bloqueos, política de contraseñas).
- Backend de dominio: controladores, modelos, migraciones y policies (hoy no existen).
- Reemplazo de los stores de `localStorage` por llamadas al servidor y autenticación real de Laravel.
- Estrategia para actualizar GSAP/Alpine vendorizados y para recompilar CSS en servidor de producción.
- Capa frontend: Blade (decidida de hecho), Inertia o Livewire.
- Numeración, impuestos y PDF/impresión de facturas.
- Métodos de pago y manejo de anulaciones.
- Fórmula de comisión por lavado.
- Función de aptitud y restricciones del algoritmo genético.
- Datos de entrada/salida y módulo de la interfaz donde se mostrará la metaheurística.
- Identificación de vehículos/clientes y reglas de entrada/salida.
- Nombres de modelos/tablas: inglés o español.
