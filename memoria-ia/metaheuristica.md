# Metaheurística

## Definición
Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.

## Estado
- Definido por el usuario/profesora.
- **No implementado** en el código actual (el frontend es demo con datos en `localStorage`).

## Propósito
- Ordenar y asignar los vehículos que esperan servicio y los trabajadores disponibles.
- Servir como apoyo a la planificación operativa: qué vehículo atender y con qué trabajador.

## Ubicación en la interfaz
No es un módulo aparte: se integra en dos módulos existentes.
- **Vehículos y entradas/salidas** (`/vehiculos`): ordenamiento de los vehículos que esperan servicio.
- **Lavados y comisiones** (`/lavados`): ordenamiento y asignación de los trabajadores disponibles a cada vehículo.

El resultado se muestra como una vista de planificación dentro de esos módulos: orden de atención de vehículos y asignación trabajador–vehículo.

## Pendiente de definir (no asumir)
- Función de aptitud: qué se minimiza o maximiza (tiempo de espera, carga de trabajo, comisiones, etc.).
- Restricciones: turnos, disponibilidad, tamaño del vehículo, especialidad del trabajador, capacidad del local.
- Datos de entrada y salida del algoritmo.
- Parámetros genéticos: tamaño de población, generaciones, selección, cruce y mutación.
- Dónde se ejecuta (backend/job) y diseño exacto de la vista dentro de Vehículos y Lavados.
