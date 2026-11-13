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

## Lavados y comisiones
- Cada lavado registra vehículo, tamaño, empleado, fecha/hora, precio y comisión.
- La comisión se genera por carro lavado y pertenece al empleado que lo lavó.
- El historial debe mostrar la fecha del lavado.
- Aquí se muestra el ordenamiento y la asignación de los trabajadores disponibles a los vehículos (resultado de la metaheurística).
- **Pendiente:** fórmula de comisión, empleados por turno, servicios adicionales, registro de empleados (hoy hardcodeados en el demo).

## Metaheurística / planificación
- Tipo: algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.
- Estado: definido; no implementado.
- No es un módulo aparte: se integra en **Vehículos y entradas/salidas** (ordenamiento de vehículos) y **Lavados y comisiones** (ordenamiento/asignación de trabajadores disponibles).
- **Pendiente:** función de aptitud, restricciones, datos de entrada/salida, parámetros genéticos y diseño exacto de la vista dentro de esos módulos.

## Auditoría forense
- Bitácora persistida en base de datos, no solo en `storage/logs`.
- Registrar acciones críticas: cobros, facturas, comisiones, movimientos de inventario y accesos.
- Cada registro incluye usuario, acción, entidad afectada y timestamp.
- No debe editarse ni borrarse desde la UI; no hacer borrado físico de registros financieros/de auditoría.

## Seguridad
- Autenticación y permisos por rol.
- **Pendiente:** roles exactos, 2FA, bloqueo por intentos, política de contraseñas.
