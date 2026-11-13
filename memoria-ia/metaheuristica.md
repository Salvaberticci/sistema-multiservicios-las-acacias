# Metaheurística

## Definición
Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.

## Estado
- Definido por el usuario/profesora.
- **Prototipo frontend de demostración implementado** (2026-11-13). La versión definitiva no está implementada: el frontend es demo con datos en `localStorage` y la función de aptitud sigue pendiente.

## Propósito
- Ordenar y asignar los vehículos que esperan servicio y los trabajadores disponibles.
- Servir como apoyo a la planificación operativa: qué vehículo atender y con qué trabajador.

## Ubicación en la interfaz
No es un módulo aparte: se integra en dos módulos existentes.
- **Vehículos y entradas/salidas** (`/vehiculos/planificacion`): ordenamiento de los vehículos que esperan servicio.
- **Lavados y comisiones** (`/lavados/planificacion`): ordenamiento y asignación de los trabajadores disponibles a cada vehículo.

El resultado se muestra como una vista de planificación dentro de esos módulos: orden de atención de vehículos y asignación trabajador–vehículo.

## Prototipo de demostración
- Motor: `public/js/metaheuristica.js` (script global, sin npm).
- Orden de vehículos: algoritmo genético con selección por torneo, cruce OX y mutación por intercambio.
- Asignación de trabajadores: algoritmo genético que equilibra la carga por tamaño del vehículo.
- La aptitud es **provisional** (antigüedad en cola + tamaño; carga equilibrada) y está marcada como demostración en la UI.
- Parámetros ajustables en pantalla: población, generaciones y mutación (semilla fija para reproducibilidad).

## Pendiente de definir (no asumir)
- Función de aptitud: qué se minimiza o maximiza (tiempo de espera, carga de trabajo, comisiones, etc.).
- Restricciones: turnos, disponibilidad, tamaño del vehículo, especialidad del trabajador, capacidad del local.
- Datos de entrada y salida del algoritmo.
- Parámetros genéticos definitivos: tamaño de población, generaciones, selección, cruce y mutación.
- Dónde se ejecuta en la versión final (backend/job) y cómo se persiste la planificación.
