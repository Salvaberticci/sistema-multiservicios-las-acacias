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
- **Pendiente:** placa/identificador, cliente, estado dentro/fuera, tarifa por tamaño, estacionamiento.

## Lavados y comisiones
- Cada lavado registra vehículo, tamaño, empleado, fecha/hora, precio y comisión.
- La comisión se genera por carro lavado y pertenece al empleado que lo lavó.
- El historial debe mostrar la fecha del lavado.
- **Pendiente:** fórmula de comisión, empleados por turno, servicios adicionales.

## Auditoría forense
- Bitácora persistida en base de datos, no solo en `storage/logs`.
- Registrar acciones críticas: cobros, facturas, comisiones, movimientos de inventario y accesos.
- Cada registro incluye usuario, acción, entidad afectada y timestamp.
- No debe editarse ni borrarse desde la UI; no hacer borrado físico de registros financieros/de auditoría.

## Seguridad
- Autenticación y permisos por rol.
- **Pendiente:** roles exactos, 2FA, bloqueo por intentos, política de contraseñas.
