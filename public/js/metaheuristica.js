// Motor de demostración de la metaheurística.
// Algoritmo genético adaptado al ordenamiento de vehículos y trabajadores disponibles.
// IMPORTANTE: la función de aptitud definitiva está pendiente de definición.
// Estas propuestas son una demostración frontend, no la regla de negocio final.
window.Metaheuristica = (() => {
    const DEFAULTS = {
        poblacion: 40,
        generaciones: 60,
        mutacion: 0.2,
        elitismo: 2,
        semilla: 42
    };

    const PESO_TAMANO = { 'pequeño': 1, 'mediano': 1.5, 'grande': 2 };

    // PRNG determinista para que la demostración sea reproducible.
    function crearRandom(semilla) {
        let state = semilla >>> 0;
        return () => {
            state = (state + 0x6d2b79f5) | 0;
            let t = Math.imul(state ^ (state >>> 15), 1 | state);
            t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
            return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
        };
    }

    function barajar(lista, random) {
        const copia = [...lista];
        for (let i = copia.length - 1; i > 0; i--) {
            const j = Math.floor(random() * (i + 1));
            [copia[i], copia[j]] = [copia[j], copia[i]];
        }
        return copia;
    }

    // Cruce por orden (OX): mantiene un segmento y completa con el orden del otro padre.
    function cruceOX(padreA, padreB, random) {
        const n = padreA.length;
        const inicio = Math.floor(random() * n);
        const fin = inicio + Math.floor(random() * (n - inicio));
        const hijo = new Array(n).fill(null);
        const usados = new Set();

        for (let i = inicio; i <= fin; i++) {
            hijo[i] = padreA[i];
            usados.add(padreA[i]);
        }

        let cursor = (fin + 1) % n;
        for (let i = 0; i < n; i++) {
            const valor = padreB[(fin + 1 + i) % n];
            if (!usados.has(valor)) {
                hijo[cursor] = valor;
                usados.add(valor);
                cursor = (cursor + 1) % n;
            }
        }
        return hijo;
    }

    function mutarIntercambio(individuo, random, tasa) {
        if (random() > tasa || individuo.length < 2) return individuo;
        const copia = [...individuo];
        const a = Math.floor(random() * copia.length);
        let b = Math.floor(random() * copia.length);
        if (a === b) b = (b + 1) % copia.length;
        [copia[a], copia[b]] = [copia[b], copia[a]];
        return copia;
    }

    function seleccionTorneo(poblacion, aptitudes, random, tamano = 3) {
        let mejor = null;
        for (let i = 0; i < tamano; i++) {
            const idx = Math.floor(random() * poblacion.length);
            if (mejor === null || aptitudes[idx] < aptitudes[mejor]) mejor = idx;
        }
        return poblacion[mejor];
    }

    function esperaMinutos(vehiculo, ahora) {
        const entrada = vehiculo?.entrada?.fechaEntrada;
        if (!entrada) return 0;
        return Math.max(0, Math.round((ahora - new Date(entrada).getTime()) / 60000));
    }

    // Ordena los vehículos en espera minimizando el tiempo de finalización ponderado.
    // Peso provisional: antigüedad en la cola + tamaño del vehículo.
    function ordenarVehiculos(vehiculos, opciones = {}) {
        const opts = { ...DEFAULTS, ...opciones };
        const lista = [...vehiculos];
        const ahora = Date.now();

        if (lista.length <= 1) {
            return {
                orden: lista.map((v, i) => ({ ...v, posicion: i + 1, prioridad: 1, esperaMin: esperaMinutos(v, ahora) })),
                fitness: 0,
                generaciones: 0,
                historial: []
            };
        }

        const random = crearRandom(opts.semilla);
        const n = lista.length;
        const pesos = lista.map(v => {
            const espera = esperaMinutos(v, ahora);
            const peso = 1 + Math.min(espera, 240) / 120 + (PESO_TAMANO[v.tamano] || 1);
            return Math.round(peso * 100) / 100;
        });

        const aptitud = (orden) => orden.reduce((suma, idx, posicion) => suma + pesos[idx] * (posicion + 1), 0);

        // Población inicial: FIFO (por antigüedad) + permutaciones aleatorias.
        const fifo = [...Array(n).keys()].sort((a, b) => {
            const ea = lista[a]?.entrada?.fechaEntrada || 0;
            const eb = lista[b]?.entrada?.fechaEntrada || 0;
            return new Date(ea) - new Date(eb);
        });

        let poblacion = [fifo];
        while (poblacion.length < opts.poblacion) {
            poblacion.push(barajar([...Array(n).keys()], random));
        }

        let mejor = [...fifo];
        let mejorAptitud = aptitud(mejor);
        const historial = [];

        for (let gen = 0; gen < opts.generaciones; gen++) {
            const aptitudes = poblacion.map(aptitud);
            const ordenados = poblacion
                .map((ind, i) => ({ ind, apt: aptitudes[i] }))
                .sort((a, b) => a.apt - b.apt);

            if (ordenados[0].apt < mejorAptitud) {
                mejorAptitud = ordenados[0].apt;
                mejor = [...ordenados[0].ind];
            }
            historial.push(Math.round(mejorAptitud * 100) / 100);

            const siguiente = ordenados.slice(0, opts.elitismo).map(o => [...o.ind]);
            while (siguiente.length < opts.poblacion) {
                const padreA = seleccionTorneo(poblacion, aptitudes, random);
                const padreB = seleccionTorneo(poblacion, aptitudes, random);
                let hijo = cruceOX(padreA, padreB, random);
                hijo = mutarIntercambio(hijo, random, opts.mutacion);
                siguiente.push(hijo);
            }
            poblacion = siguiente;
        }

        return {
            orden: mejor.map((idx, posicion) => ({
                ...lista[idx],
                posicion: posicion + 1,
                prioridad: pesos[idx],
                esperaMin: esperaMinutos(lista[idx], ahora)
            })),
            fitness: Math.round(mejorAptitud * 100) / 100,
            generaciones: opts.generaciones,
            historial
        };
    }

    // Asigna cada vehículo a un trabajador equilibrando la carga (peso por tamaño).
    // Es una asignación de demostración: no considera aún especialidades ni turnos.
    function asignarTrabajadores(vehiculos, trabajadores, opciones = {}) {
        const opts = { ...DEFAULTS, ...opciones };
        const lista = [...vehiculos];
        const workers = [...trabajadores];

        if (!lista.length || !workers.length) {
            return { asignaciones: [], cargas: [], fitness: 0, generaciones: 0, historial: [] };
        }

        const random = crearRandom(opts.semilla);
        const n = lista.length;
        const m = workers.length;
        const pesos = lista.map(v => PESO_TAMANO[v.tamano] || 1);

        const calcularCargas = (cromosoma) => {
            const cargas = new Array(m).fill(0);
            cromosoma.forEach((workerIdx, i) => { cargas[workerIdx] += pesos[i]; });
            return cargas;
        };

        const aptitud = (cromosoma) => {
            const cargas = calcularCargas(cromosoma);
            const maximo = Math.max(...cargas);
            const promedio = cargas.reduce((a, b) => a + b, 0) / m;
            const varianza = cargas.reduce((suma, c) => suma + Math.pow(c - promedio, 2), 0) / m;
            return maximo * 2 + varianza;
        };

        const cromosomaAleatorio = () => Array.from({ length: n }, () => Math.floor(random() * m));

        let poblacion = [Array.from({ length: n }, (_, i) => i % m)];
        while (poblacion.length < opts.poblacion) poblacion.push(cromosomaAleatorio());

        let mejor = [...poblacion[0]];
        let mejorAptitud = aptitud(mejor);
        const historial = [];

        for (let gen = 0; gen < opts.generaciones; gen++) {
            const aptitudes = poblacion.map(aptitud);
            const ordenados = poblacion
                .map((ind, i) => ({ ind, apt: aptitudes[i] }))
                .sort((a, b) => a.apt - b.apt);

            if (ordenados[0].apt < mejorAptitud) {
                mejorAptitud = ordenados[0].apt;
                mejor = [...ordenados[0].ind];
            }
            historial.push(Math.round(mejorAptitud * 100) / 100);

            const siguiente = ordenados.slice(0, opts.elitismo).map(o => [...o.ind]);
            while (siguiente.length < opts.poblacion) {
                const padreA = seleccionTorneo(poblacion, aptitudes, random);
                const padreB = seleccionTorneo(poblacion, aptitudes, random);
                const hijo = padreA.map((genA, i) => (random() < 0.5 ? genA : padreB[i]));
                if (random() < opts.mutacion) {
                    hijo[Math.floor(random() * n)] = Math.floor(random() * m);
                }
                siguiente.push(hijo);
            }
            poblacion = siguiente;
        }

        const cargas = calcularCargas(mejor);
        const asignaciones = lista.map((vehiculo, i) => ({
            vehiculo,
            trabajador: workers[mejor[i]],
            carga: pesos[i]
        }));

        return {
            asignaciones,
            cargas: workers.map((trabajador, i) => ({
                trabajador,
                carga: Math.round(cargas[i] * 100) / 100,
                vehiculos: mejor.filter(w => w === i).length
            })),
            fitness: Math.round(mejorAptitud * 100) / 100,
            generaciones: opts.generaciones,
            historial
        };
    }

    return { ordenarVehiculos, asignarTrabajadores, DEFAULTS };
})();
