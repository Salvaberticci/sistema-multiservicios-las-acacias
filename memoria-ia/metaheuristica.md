# Metaheurística

## Definición
Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.

## Estado
- Definido por el usuario/profesora.
- **No implementado** en el código actual (el frontend es demo con datos en `localStorage`).

## Propósito
- Ordenar y asignar los vehículos que esperan servicio y los trabajadores disponibles.
- Servir como apoyo a la planificación operativa: qué vehículo atender y con qué trabajador.

## Pendiente de definir (no asumir)
- Función de aptitud: qué se minimiza o maximiza (tiempo de espera, carga de trabajo, comisiones, etc.).
- Restricciones: turnos, disponibilidad, tamaño del vehículo, especialidad del trabajador, capacidad del local.
- Datos de entrada y salida del algoritmo.
- Parámetros genéticos: tamaño de población, generaciones, selección, cruce y mutación.
- Dónde se ejecuta (backend/job) y cómo se muestra en la interfaz.
- Cómo se integra con los módulos de Vehículos, Lavados y Comisiones.
