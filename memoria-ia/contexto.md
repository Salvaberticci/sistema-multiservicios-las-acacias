# Contexto

## Negocio
**Multiservicios Las Acacias** es un negocio de servicios para vehículos (lavado) que necesita administrar cobros, facturación, inventario, entradas/salidas de vehículos y comisiones de empleados.

## Alcance inicial
- Cobro y facturación de servicios.
- Inventario de productos/insumos.
- Registro de entrada y salida de vehículos.
- Clasificación del vehículo por tamaño: pequeño, mediano o grande.
- Lavados: empleado que lava, vehículo, fecha/hora, precio y comisión.
- Historial de lavados con fecha visible.
- Auditoría forense de acciones críticas.
- Seguridad: autenticación y permisos por rol.

## Idioma
- UI, mensajes y etiquetas de negocio en español.
- El código puede seguir las convenciones de Laravel; no se ha definido si modelos/tablas van en inglés o español. **Pendiente.**

## Estado
- Laravel 12 scaffolded con frontend demo: Blade + Tailwind 4 + Alpine + GSAP.
- Los datos viven en `localStorage`; no hay backend de dominio ni autenticación real.
- Repositorio git inicializado y publicado en GitHub: `Salvaberticci/sistema-multiservicios-las-acacias` (rama `main`).
- La guía de instalación y ejecución local está en `README.md`.
