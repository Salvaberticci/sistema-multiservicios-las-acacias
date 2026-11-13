# Módulos y reglas de negocio

> Estado: requisitos intencionales. Ajustar cuando se implementen y validar con el usuario.

## Facturación y cobros
- Registrar servicios cobrados y generar facturas.
- Calcular totales en el servidor.
- **Pendiente:** numeración, impuestos, PDF/impresión, métodos de pago, anulaciones.

## Inventario
- Registrar productos/insumos y movimientos de stock.
- **Pendiente:** unidad de medida, stock mínimo, consumo por lavado, proveedores.

## Vehículos y entradas/salidas
- Registrar entrada y salida de cada vehículo.
- Tamaño obligatorio: pequeño, mediano o grande.
- Aquí se muestra el ordenamiento de los vehículos que esperan servicio (resultado de la metaheurística).
- **Pendiente:** placa/identificador, cliente, estado dentro/fuera, tarifa por tamaño, estacionamiento.

## Empleados / personal
- Registro del personal de lavado: nombre, teléfono, email, cargo, turno, fecha de ingreso y estado (activo/inactivo).
- Los empleados activos son los trabajadores disponibles para la asignación de lavados.
- Pantallas: listado con filtros, alta y edición (`/empleados`).
- **Pendiente:** relación formal con usuarios del sistema, especialidades y disponibilidad por horario.

## Lavados y comisiones
- Cada lavado registra vehículo, tamaño, empleado, fecha/hora, precio y comisión.
- La comisión se genera por carro lavado y pertenece al empleado que lo lavó.
- El historial debe mostrar la fecha del lavado.
- El alta de lavados toma los empleados activos del módulo Empleados.
- Aquí se muestra el ordenamiento y la asignación de los trabajadores disponibles a los vehículos (resultado de la metaheurística).
- **Pendiente:** fórmula de comisión, servicios adicionales, reglas de asignación por turno.

## Metaheurística / planificación
- Tipo: algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.
- Estado: prototipo frontend de demostración; la versión definitiva no está implementada.
- No es un módulo aparte: se integra en **Vehículos y entradas/salidas** (`/vehiculos/planificacion`) y **Lavados y comisiones** (`/lavados/planificacion`).
- El prototipo ordena vehículos en espera y asigna trabajadores activos con un algoritmo genético en JS (`public/js/metaheuristica.js`).
- **Pendiente:** función de aptitud definitiva, restricciones, datos de entrada/salida y parámetros validados con la profesora.

## Auditoría forense
- Bitácora persistida en base de datos, no solo en `storage/logs`.
- Registrar acciones críticas: cobros, facturas, comisiones, movimientos de inventario y accesos.
- Cada registro incluye usuario, acción, entidad afectada y timestamp.
- No debe editarse ni borrarse desde la UI; no hacer borrado físico de registros financieros/de auditoría.

## Seguridad
- Autenticación y permisos por rol.
- **Pendiente:** roles exactos, 2FA, bloqueo por intentos, política de contraseñas.
