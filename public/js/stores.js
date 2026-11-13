// Stores globales - Persistencia en localStorage
const Stores = {};
window.Stores = Stores;

// Utilidad de almacenamiento
const Storage = {
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch {
            return defaultValue;
        }
    },
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('Storage set error:', e);
        }
    },
    remove(key) {
        localStorage.removeItem(key);
    }
};

// Generador de IDs
const uid = () => Date.now().toString(36) + Math.random().toString(36).substr(2, 9);

// PRNG determinista: los datos demo son iguales en todos los navegadores
const Demo = (() => {
    let state = 0x5f3759df;
    const rnd = () => {
        state |= 0;
        state = (state + 0x6d2b79f5) | 0;
        let t = Math.imul(state ^ (state >>> 15), 1 | state);
        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
    return {
        rnd,
        int: (min, max) => min + Math.floor(rnd() * (max - min + 1)),
        pick: (list) => list[Math.floor(rnd() * list.length)],
        money: (value) => Math.round(value * 100) / 100,
        daysAgo: (days, hour, minute) => {
            const date = new Date();
            date.setDate(date.getDate() - days);
            date.setHours(hour, minute || 0, 0, 0);
            return date;
        },
        iso: (date) => date.toISOString()
    };
})();

// Formato de fecha
const formatDate = (date) => {
    const d = new Date(date);
    return d.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const formatDateShort = (date) => {
    const d = new Date(date);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount);
};

// ============================================
// STORE: AUTH
// ============================================
Stores.auth = {
    user: null,
    token: null,
    isAuthenticated: false,
    
    init() {
        const saved = Storage.get('auth');
        if (saved) {
            this.user = saved.user;
            this.token = saved.token;
            this.isAuthenticated = !!saved.user;
        }
        return this;
    },
    
    login(email, password, remember = false) {
        // Demo credentials
        const users = {
            'admin@acacias.com': { id: 1, name: 'Administrador', email: 'admin@acacias.com', role: 'admin', avatar: null, password: 'admin123' },
            'operador@acacias.com': { id: 2, name: 'Operador', email: 'operador@acacias.com', role: 'operador', avatar: null, password: 'operador123' },
            'cajero@acacias.com': { id: 3, name: 'Cajero', email: 'cajero@acacias.com', role: 'cajero', avatar: null, password: 'cajero123' },
            'auditor@acacias.com': { id: 4, name: 'Auditor', email: 'auditor@acacias.com', role: 'auditor', avatar: null, password: 'auditor123' }
        };
        
        const user = users[email];
        if (user && user.password === password) {
            const { password: _, ...userData } = user;
            this.user = userData;
            this.token = 'demo-token-' + uid();
            this.isAuthenticated = true;
            Storage.set('auth', { user: this.user, token: this.token });
            return Promise.resolve(this.user);
        }
        return Promise.reject(new Error('Credenciales inválidas'));
    },
    
    logout() {
        this.user = null;
        this.token = null;
        this.isAuthenticated = false;
        Storage.remove('auth');
    },
    
    hasRole(role) {
        if (!this.user) return false;
        const roles = Array.isArray(role) ? role : [role];
        return roles.includes(this.user.role);
    }
};

// ============================================
// STORE: FACTURAS
// ============================================
Stores.facturas = {
    items: [],
    draft: null,
    
    init() {
        const saved = Storage.get('facturas');
        if (saved) {
            this.items = saved.items || [];
            this.draft = saved.draft || null;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const clientes = Stores.clientes.items || [];
        const servicios = [
            { nombre: 'Lavado exterior', precio: 12 },
            { nombre: 'Lavado interior', precio: 18 },
            { nombre: 'Lavado completo', precio: 28 },
            { nombre: 'Pulido de faros', precio: 22 },
            { nombre: 'Aspirado de tapicería', precio: 10 },
            { nombre: 'Limpieza de motor', precio: 32 },
            { nombre: 'Tratamiento ozono', precio: 38 },
            { nombre: 'Encerado premium', precio: 24 }
        ];
        const estados = ['pagada', 'pagada', 'pagada', 'pendiente', 'pendiente', 'borrador', 'anulada'];
        const metodos = ['efectivo', 'tarjeta', 'transferencia'];

        this.items = [];
        for (let i = 0; i < 64; i++) {
            const esHoy = i < 8;
            const fecha = esHoy
                ? new Date(Date.now() - Demo.int(1, 8) * 3600000)
                : Demo.daysAgo(Demo.int(1, 75), Demo.int(8, 19), Demo.int(0, 59));
            const cliente = clientes[i % Math.max(clientes.length, 1)] || { id: 1, nombre: 'Cliente de mostrador', telefono: '' };

            const lineas = [];
            let subtotal = 0;
            const numLineas = Demo.int(1, 3);
            for (let j = 0; j < numLineas; j++) {
                const servicio = Demo.pick(servicios);
                const cantidad = Demo.int(1, 2);
                const lineaSub = Demo.money(servicio.precio * cantidad);
                subtotal += lineaSub;
                lineas.push({
                    id: 'lin-' + i + '-' + j,
                    servicio: servicio.nombre,
                    cantidad: cantidad,
                    precioUnitario: servicio.precio,
                    subtotal: lineaSub
                });
            }

            const iva = Demo.money(subtotal * 0.21);
            this.items.push({
                id: 'fac-demo-' + (i + 1),
                numero: 'FAC-' + String(i + 1).padStart(5, '0'),
                clienteId: cliente.id,
                clienteNombre: cliente.nombre,
                clienteTelefono: cliente.telefono,
                lineas: lineas,
                subtotal: Demo.money(subtotal),
                iva: iva,
                total: Demo.money(subtotal + iva),
                estado: esHoy ? Demo.pick(['pagada', 'pendiente']) : Demo.pick(estados),
                fecha: Demo.iso(fecha),
                metodoPago: Demo.pick(metodos)
            });
        }

        this.save();
    },

    
    save() {
        Storage.set('facturas', { items: this.items, draft: this.draft });
    },
    
    getAll() {
        return [...this.items].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
    },
    
    getById(id) {
        return this.items.find(f => f.id === id);
    },
    
    saveDraft(data) {
        this.draft = { ...this.draft, ...data };
        this.save();
    },
    
    create(data) {
        const factura = {
            id: uid(),
            numero: `FAC-${String(this.items.length + 1).padStart(5, '0')}`,
            ...data,
            fecha: new Date().toISOString()
        };
        this.items.unshift(factura);
        this.draft = null;
        this.save();
        return factura;
    },
    
    update(id, data) {
        const idx = this.items.findIndex(f => f.id === id);
        if (idx !== -1) {
            this.items[idx] = { ...this.items[idx], ...data };
            this.save();
        }
    },
    
    anular(id) {
        const factura = this.getById(id);
        if (factura && factura.estado !== 'anulada') {
            factura.estado = 'anulada';
            this.save();
            Stores.auditoria.log('anular', 'factura', id, { antes: factura.estado });
        }
    },
    
    getStats() {
        const hoy = new Date().toDateString();
        const hoyItems = this.items.filter(f => new Date(f.fecha).toDateString() === hoy);
        return {
            totalHoy: hoyItems.length,
            totalPendiente: this.items.filter(f => f.estado === 'pendiente').length,
            ingresoHoy: hoyItems.reduce((sum, f) => sum + f.total, 0)
        };
    }
};

// ============================================
// STORE: CLIENTES
// ============================================
Stores.clientes = {
    items: [],
    
    init() {
        const saved = Storage.get('clientes');
        if (saved) {
            this.items = saved;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const base = [
            { nombre: 'Lucía Ramírez', telefono: '612 480 931', email: 'lucia.ramirez@email.com', direccion: 'Calle Mayor 42' },
            { nombre: 'Javier Molina', telefono: '634 118 207', email: 'javier.molina@email.com', direccion: 'Av. del Puerto 18' },
            { nombre: 'María Ortega', telefono: '655 903 214', email: 'maria.ortega@email.com', direccion: 'Calle Olivo 7' },
            { nombre: 'Carlos Ferrer', telefono: '601 774 382', email: 'carlos.ferrer@email.com', direccion: 'Paseo de la Estación 95' },
            { nombre: 'Ana Beltrán', telefono: '688 221 540', email: 'ana.beltran@email.com', direccion: 'Calle Azahar 12' },
            { nombre: 'Sergio Navarro', telefono: '622 309 118', email: 'sergio.navarro@email.com', direccion: 'Av. de la Constitución 60' },
            { nombre: 'Elena Vidal', telefono: '677 512 046', email: 'elena.vidal@email.com', direccion: 'Calle del Río 3' },
            { nombre: 'Pedro Salas', telefono: '645 830 771', email: 'pedro.salas@email.com', direccion: 'Camino Viejo 24' },
            { nombre: 'Marta Ibáñez', telefono: '699 204 315', email: 'marta.ibanez@email.com', direccion: 'Calle Alameda 51' },
            { nombre: 'Diego Romero', telefono: '616 447 890', email: 'diego.romero@email.com', direccion: 'Av. de los Reyes 8' },
            { nombre: 'Paula Serrano', telefono: '630 992 154', email: 'paula.serrano@email.com', direccion: 'Calle Jardines 33' },
            { nombre: 'Andrés Cabrera', telefono: '662 118 473', email: 'andres.cabrera@email.com', direccion: 'Ronda Norte 71' }
        ];
        this.items = base.map((cliente, index) => Object.assign({ id: index + 1 }, cliente));
        this.save();
    },

    
    save() {
        Storage.set('clientes', this.items);
    },
    
    getAll() {
        return [...this.items].sort((a, b) => a.nombre.localeCompare(b.nombre));
    },
    
    getById(id) {
        return this.items.find(c => c.id == id);
    },
    
    create(data) {
        const cliente = { id: Date.now(), ...data };
        this.items.push(cliente);
        this.save();
        return cliente;
    }
};

// ============================================
// STORE: VEHICULOS
// ============================================
Stores.vehiculos = {
    items: [],
    
    init() {
        const saved = Storage.get('vehiculos');
        if (saved) {
            this.items = saved;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const tamanos = ['pequeño', 'mediano', 'grande'];
        const placas = [
            '4821KLM', '7315BTR', '9042GHD', '1187JCP', '5620MFX',
            '8453NZA', '2906PLS', '6678RVC', '3341DWH', '7754KGB',
            '1092XTR', '9218LMD', '4507NFC', '8830JRP', '2165TVS',
            '5793BKD', '6420GSM', '3078HZN', '7641PRC', '1594WLK',
            '8256DVB', '4913MGT', '6087RFH', '9720CSN', '2354KLP', '5189TZB'
        ];
        const clientes = Stores.clientes.items || [];

        this.items = placas.map((placa, index) => {
            const cliente = clientes[index % Math.max(clientes.length, 1)] || { id: 1, nombre: 'Cliente de mostrador' };
            const tamano = Demo.pick(tamanos);
            const dentro = index < 10;
            const historial = [];

            for (let h = 0; h < Demo.int(2, 5); h++) {
                const fechaEntrada = Demo.daysAgo(Demo.int(1, 45), Demo.int(8, 18), Demo.int(0, 59));
                const fechaSalida = new Date(fechaEntrada.getTime() + Demo.int(1, 7) * 3600000);
                historial.push({
                    id: 'hist-' + index + '-' + h,
                    fechaEntrada: Demo.iso(fechaEntrada),
                    fechaSalida: Demo.iso(fechaSalida),
                    tarifa: Demo.money(Demo.int(10, 38)),
                    metodoPago: Demo.pick(['efectivo', 'tarjeta', 'transferencia'])
                });
            }

            return {
                id: 'veh-demo-' + (index + 1),
                placa: placa,
                clienteId: cliente.id,
                clienteNombre: cliente.nombre,
                tamano: tamano,
                estado: dentro ? 'dentro' : 'fuera',
                entrada: dentro ? {
                    id: 'entrada-' + (index + 1),
                    fechaEntrada: Demo.iso(new Date(Date.now() - Demo.int(1, 6) * 3600000)),
                    observaciones: ''
                } : null,
                historial: historial
            };
        });

        this.save();
    },

    
    save() {
        Storage.set('vehiculos', this.items);
    },
    
    getAll() {
        return [...this.items].sort((a, b) => {
            if (a.estado === 'dentro' && b.estado === 'fuera') return -1;
            if (a.estado === 'fuera' && b.estado === 'dentro') return 1;
            return new Date(b.entrada?.fechaEntrada || 0) - new Date(a.entrada?.fechaEntrada || 0);
        });
    },
    
    getDentro() {
        return this.items.filter(v => v.estado === 'dentro');
    },
    
    getFuera() {
        return this.items.filter(v => v.estado === 'fuera');
    },
    
    getById(id) {
        return this.items.find(v => v.id === id);
    },
    
    getByPlaca(placa) {
        return this.items.find(v => v.placa === placa);
    },
    
    registrarEntrada(data) {
        const vehiculo = this.getByPlaca(data.placa.toUpperCase());
        const now = new Date().toISOString();
        
        if (vehiculo) {
            vehiculo.estado = 'dentro';
            vehiculo.entrada = { id: uid(), fechaEntrada: now, observaciones: data.observaciones };
            vehiculo.tamano = data.tamano;
            vehiculo.clienteId = data.clienteId;
            vehiculo.clienteNombre = Stores.clientes.getById(data.clienteId)?.nombre || '';
        } else {
            const cliente = Stores.clientes.getById(data.clienteId);
            this.items.push({
                id: uid(),
                placa: data.placa.toUpperCase(),
                clienteId: data.clienteId,
                clienteNombre: cliente?.nombre || '',
                tamano: data.tamano,
                estado: 'dentro',
                entrada: { id: uid(), fechaEntrada: now, observaciones: data.observaciones },
                historial: []
            });
        }
        this.save();
        Stores.auditoria.log('crear', 'vehiculo_entrada', data.placa, { tamano: data.tamano });
    },
    
    registrarSalida(placa, data) {
        const vehiculo = this.getByPlaca(placa);
        if (!vehiculo || !vehiculo.entrada) return null;
        
        const now = new Date();
        const entrada = new Date(vehiculo.entrada.fechaEntrada);
        const minutos = (now - entrada) / 60000;
        const horas = Math.ceil(minutos / 60);
        
        const tarifas = Stores.settings.getTarifas();
        const tarifaBase = tarifas[vehiculo.tamano] || 20;
        const recargo = tarifas.recargoHora || 5;
        const tarifa = tarifaBase + Math.max(0, horas - 1) * recargo;
        
        const registro = {
            id: vehiculo.entrada.id,
            fechaEntrada: vehiculo.entrada.fechaEntrada,
            fechaSalida: now.toISOString(),
            tarifa: Math.round(tarifa * 100) / 100,
            metodoPago: data.metodoPago,
            observaciones: data.observaciones
        };
        
        vehiculo.historial.unshift(registro);
        vehiculo.estado = 'fuera';
        vehiculo.entrada = null;
        this.save();
        
        Stores.auditoria.log('crear', 'vehiculo_salida', placa, { tarifa: registro.tarifa, duracionMin: Math.round(minutos) });
        return { vehiculo, tarifa: registro.tarifa, duracionMin: Math.round(minutos) };
    },
    
    getStats() {
        return {
            dentro: this.items.filter(v => v.estado === 'dentro').length,
            fuera: this.items.filter(v => v.estado === 'fuera').length,
            total: this.items.length
        };
    }
};

// ============================================
// STORE: LAVADOS
// ============================================
Stores.lavados = {
    items: [],
    
    init() {
        const saved = Storage.get('lavados');
        if (saved) {
            this.items = saved;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const vehiculos = Stores.vehiculos.items || [];
        const empleados = [
            { id: 1, nombre: 'Carlos Ruiz' },
            { id: 2, nombre: 'Miguel Torres' },
            { id: 3, nombre: 'Javier Ortiz' },
            { id: 4, nombre: 'Antonio Vega' },
            { id: 5, nombre: 'Sofía Delgado' }
        ];
        const precios = { 'pequeño': 12, 'mediano': 20, 'grande': 30 };

        this.items = [];
        for (let i = 0; i < 220; i++) {
            const vehiculo = Demo.pick(vehiculos);
            const empleado = Demo.pick(empleados);
            const tamano = vehiculo.tamano || Demo.pick(['pequeño', 'mediano', 'grande']);
            const precio = precios[tamano];
            const esHoy = i < 14;
            const fecha = esHoy
                ? new Date(Date.now() - Demo.int(0, 8) * 3600000)
                : Demo.daysAgo(Demo.int(1, 90), Demo.int(8, 19), Demo.int(0, 59));

            this.items.push({
                id: 'lav-demo-' + (i + 1),
                vehiculoId: vehiculo.id,
                vehiculoPlaca: vehiculo.placa,
                vehiculoTamano: tamano,
                clienteNombre: vehiculo.clienteNombre,
                empleadoId: empleado.id,
                empleadoNombre: empleado.nombre,
                fecha: Demo.iso(fecha),
                precio: precio,
                comision: Demo.money(precio * Stores.settings.getComision(tamano)),
                estado: Demo.rnd() < 0.035 ? 'anulado' : 'completado',
                observaciones: ''
            });
        }

        this.save();
    },

    
    save() {
        Storage.set('lavados', this.items);
    },
    
    getAll() {
        return [...this.items].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
    },
    
    getById(id) {
        return this.items.find(l => l.id === id);
    },
    
    create(data) {
        const comisionPct = Stores.settings.getComision(data.tamano);
        const comision = Math.round(data.precio * comisionPct * 100) / 100;
        
        const lavado = {
            id: uid(),
            ...data,
            comision,
            fecha: new Date().toISOString(),
            estado: 'completado'
        };
        this.items.unshift(lavado);
        this.save();
        Stores.auditoria.log('crear', 'lavado', lavado.id, { precio: lavado.precio, comision: lavado.comision });
        return lavado;
    },
    
    anular(id) {
        const lavado = this.getById(id);
        if (lavado && lavado.estado !== 'anulado') {
            lavado.estado = 'anulado';
            this.save();
            Stores.auditoria.log('anular', 'lavado', id);
        }
    },
    
    getStats() {
        const hoy = new Date().toDateString();
        const hoyItems = this.items.filter(l => new Date(l.fecha).toDateString() === hoy && l.estado === 'completado');
        return {
            totalHoy: hoyItems.length,
            ingresoHoy: hoyItems.reduce((sum, l) => sum + l.precio, 0),
            comisionHoy: hoyItems.reduce((sum, l) => sum + l.comision, 0),
            totalMes: this.items.filter(l => l.estado === 'completado').length
        };
    }
};

// ============================================
// STORE: INVENTARIO
// ============================================
Stores.inventario = {
    productos: [],
    movimientos: [],
    
    init() {
        const saved = Storage.get('inventario');
        if (saved) {
            this.productos = saved.productos || [];
            this.movimientos = saved.movimientos || [];
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const base = [
            { nombre: 'Champú concentrado 5L', categoria: 'Limpieza', unidad: 'L', compra: 8, venta: 15 },
            { nombre: 'Cera líquida brillo 1L', categoria: 'Ceras', unidad: 'L', compra: 12, venta: 22 },
            { nombre: 'Toallas microfibra pack 10', categoria: 'Accesorios', unidad: 'pack', compra: 18, venta: 30 },
            { nombre: 'Limpiacristales 750ml', categoria: 'Limpieza', unidad: 'ud', compra: 3, venta: 6 },
            { nombre: 'Desengrasante motor 5L', categoria: 'Químicos', unidad: 'L', compra: 15, venta: 28 },
            { nombre: 'Aspiradora profesional', categoria: 'Herramientas', unidad: 'ud', compra: 120, venta: 200 },
            { nombre: 'Papel secamanos rollo', categoria: 'Papel', unidad: 'ud', compra: 2, venta: 4.5 },
            { nombre: 'Abrillantador neumáticos 1L', categoria: 'Ceras', unidad: 'L', compra: 10, venta: 18 },
            { nombre: 'Cepillo cerdas suaves', categoria: 'Accesorios', unidad: 'ud', compra: 5, venta: 9 },
            { nombre: 'Limpiador tapicería 1L', categoria: 'Limpieza', unidad: 'L', compra: 7, venta: 13 },
            { nombre: 'Pulidora orbital', categoria: 'Herramientas', unidad: 'ud', compra: 85, venta: 150 },
            { nombre: 'Guantes nitrilo caja 100', categoria: 'Accesorios', unidad: 'caja', compra: 12, venta: 20 },
            { nombre: 'Ambientador coche variado', categoria: 'Accesorios', unidad: 'ud', compra: 1.5, venta: 3.5 },
            { nombre: 'Limpiador llantas 1L', categoria: 'Químicos', unidad: 'L', compra: 6, venta: 11 },
            { nombre: 'Chamois sintético', categoria: 'Accesorios', unidad: 'ud', compra: 4, venta: 7.5 },
            { nombre: 'Espuma activa 5L', categoria: 'Químicos', unidad: 'L', compra: 14, venta: 26 },
            { nombre: 'Cinta de carrocería', categoria: 'Accesorios', unidad: 'ud', compra: 3, venta: 6.5 },
            { nombre: 'Protector plásticos 500ml', categoria: 'Ceras', unidad: 'ud', compra: 9, venta: 16 }
        ];

        this.productos = [];
        this.movimientos = [];

        base.forEach((producto, index) => {
            const stockMin = Demo.int(4, 10);
            const stock = index < 2 ? Math.max(0, stockMin - Demo.int(1, 3)) : Demo.int(stockMin + 2, 55);
            const id = 'prod-demo-' + (index + 1);

            this.productos.push({
                id: id,
                codigo: 'INV-' + String(index + 1).padStart(4, '0'),
                nombre: producto.nombre,
                categoria: producto.categoria,
                stock: stock,
                stockMin: stockMin,
                unidad: producto.unidad,
                precioCompra: producto.compra,
                precioVenta: producto.venta,
                proveedorId: Demo.int(1, 5),
                ubicacion: 'Estante ' + String.fromCharCode(65 + (index % 5)) + '-' + ((index % 10) + 1)
            });

            this.movimientos.push({
                id: 'mov-demo-' + (index + 1),
                tipo: 'entrada',
                productoId: id,
                cantidad: stock,
                fecha: Demo.iso(Demo.daysAgo(30, Demo.int(8, 12), Demo.int(0, 59))),
                responsableId: 1,
                referencia: 'Stock inicial'
            });
        });

        for (let i = 0; i < 50; i++) {
            const producto = Demo.pick(this.productos);
            const tipo = Demo.rnd() > 0.35 ? 'salida' : 'entrada';
            this.movimientos.push({
                id: 'mov-demo-' + (19 + i),
                tipo: tipo,
                productoId: producto.id,
                cantidad: Demo.int(1, 8),
                fecha: Demo.iso(Demo.daysAgo(Demo.int(0, 29), Demo.int(8, 19), Demo.int(0, 59))),
                responsableId: Demo.int(1, 4),
                referencia: tipo === 'entrada' ? 'Compra a proveedor' : 'Consumo en lavado'
            });
        }

        this.save();
    },

    
    save() {
        Storage.set('inventario', { productos: this.productos, movimientos: this.movimientos });
    },
    
    getProductos() {
        return [...this.productos].sort((a, b) => a.nombre.localeCompare(b.nombre));
    },
    
    getProducto(id) {
        return this.productos.find(p => p.id === id);
    },
    
    createProducto(data) {
        const producto = { id: uid(), ...data, stock: data.stock || 0 };
        this.productos.push(producto);
        if (producto.stock > 0) {
            this.movimientos.push({
                id: uid(),
                tipo: 'entrada',
                productoId: producto.id,
                cantidad: producto.stock,
                fecha: new Date().toISOString(),
                responsableId: Stores.auth.user?.id || 1,
                referencia: 'Alta producto'
            });
        }
        this.save();
        return producto;
    },
    
    updateProducto(id, data) {
        const idx = this.productos.findIndex(p => p.id === id);
        if (idx !== -1) {
            this.productos[idx] = { ...this.productos[idx], ...data };
            this.save();
        }
    },
    
    addMovimiento(data) {
        const movimiento = { id: uid(), ...data, fecha: new Date().toISOString() };
        this.movimientos.unshift(movimiento);
        
        // Update stock
        const prod = this.getProducto(data.productoId);
        if (prod) {
            if (data.tipo === 'entrada') prod.stock += data.cantidad;
            else if (data.tipo === 'salida') prod.stock = Math.max(0, prod.stock - data.cantidad);
            else if (data.tipo === 'ajuste') prod.stock = Math.max(0, prod.stock + data.cantidad);
        }
        this.save();
    },
    
    getMovimientos(productoId = null) {
        let movs = [...this.movimientos].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
        if (productoId) movs = movs.filter(m => m.productoId === productoId);
        return movs;
    },
    
    getStats() {
        return {
            totalProductos: this.productos.length,
            stockBajo: this.productos.filter(p => p.stock <= p.stockMin).length,
            sinStock: this.productos.filter(p => p.stock === 0).length,
            valorStock: this.productos.reduce((sum, p) => sum + p.stock * p.precioCompra, 0)
        };
    }
};

// ============================================
// STORE: AUDITORIA
// ============================================
Stores.auditoria = {
    logs: [],
    
    init() {
        const saved = Storage.get('auditoria');
        if (saved) {
            this.logs = saved;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {

        const acciones = ['crear', 'editar', 'anular', 'login', 'exportar', 'acceso'];
        const usuarios = [
            { id: 1, nombre: 'Administrador' },
            { id: 2, nombre: 'Operador' },
            { id: 3, nombre: 'Cajero' },
            { id: 4, nombre: 'Auditor' }
        ];
        const facturas = Stores.facturas.items || [];
        const lavados = Stores.lavados.items || [];
        const vehiculos = Stores.vehiculos.items || [];
        const productos = Stores.inventario.productos || [];

        const entidades = [
            { nombre: 'factura', ids: facturas.map((f) => f.id) },
            { nombre: 'lavado', ids: lavados.map((l) => l.id) },
            { nombre: 'vehiculo', ids: vehiculos.map((v) => v.id) },
            { nombre: 'producto', ids: productos.map((p) => p.id) },
            { nombre: 'usuario', ids: usuarios.map((u) => 'user-' + u.id) }
        ];

        this.logs = [];
        for (let i = 0; i < 320; i++) {
            const accion = Demo.pick(acciones);
            const entidad = Demo.pick(entidades);
            const usuario = Demo.pick(usuarios);
            const fecha = i < 18
                ? new Date(Date.now() - Demo.int(0, 10) * 3600000)
                : Demo.daysAgo(Demo.int(1, 40), Demo.int(7, 21), Demo.int(0, 59));

            let antes = null;
            let despues = null;
            if (accion === 'editar') {
                antes = { estado: 'pendiente' };
                despues = { estado: 'pagada' };
            } else if (accion === 'crear') {
                despues = { creado: true };
            } else if (accion === 'anular') {
                antes = { estado: 'completado' };
                despues = { estado: 'anulado' };
            }

            this.logs.push({
                id: 'log-demo-' + (i + 1),
                timestamp: Demo.iso(fecha),
                usuarioId: usuario.id,
                usuarioNombre: usuario.nombre,
                accion: accion,
                entidad: entidad.nombre,
                entidadId: Demo.pick(entidad.ids.length ? entidad.ids : ['sin-registro']),
                ip: '192.168.1.' + Demo.int(2, 250),
                userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                detalles: { antes: antes, despues: despues }
            });
        }

        this.save();
    },

    
    save() {
        Storage.set('auditoria', this.logs);
    },
    
    getAll(filters = {}) {
        let logs = [...this.logs].sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
        
        if (filters.usuario) logs = logs.filter(l => l.usuarioId == filters.usuario);
        if (filters.accion) logs = logs.filter(l => l.accion === filters.accion);
        if (filters.entidad) logs = logs.filter(l => l.entidad === filters.entidad);
        if (filters.fechaDesde) logs = logs.filter(l => new Date(l.timestamp) >= new Date(filters.fechaDesde));
        if (filters.fechaHasta) logs = logs.filter(l => new Date(l.timestamp) <= new Date(filters.fechaHasta));
        if (filters.busqueda) {
            const q = filters.busqueda.toLowerCase();
            logs = logs.filter(l => 
                l.usuarioNombre.toLowerCase().includes(q) ||
                l.entidad.includes(q) ||
                l.entidadId.includes(q) ||
                JSON.stringify(l.detalles).toLowerCase().includes(q)
            );
        }
        
        return logs;
    },
    
    getById(id) {
        return this.logs.find(l => l.id === id);
    },
    
    log(accion, entidad, entidadId, detalles = {}) {
        const log = {
            id: uid(),
            timestamp: new Date().toISOString(),
            usuarioId: Stores.auth.user?.id || 1,
            usuarioNombre: Stores.auth.user?.name || 'Sistema',
            accion,
            entidad,
            entidadId,
            ip: '192.168.1.100',
            userAgent: navigator.userAgent,
            detalles
        };
        this.logs.unshift(log);
        if (this.logs.length > 1000) this.logs = this.logs.slice(0, 1000);
        this.save();
    }
};

// ============================================
// STORE: SETTINGS
// ============================================
Stores.settings = {
    data: {
        tarifas: { pequeño: 15, mediano: 25, grande: 40, recargoHora: 5 },
        comisiones: { tipo: 'porcentaje', valor: 0.15, porTamaño: { pequeño: 0.10, mediano: 0.15, grande: 0.20 }, incluyeIVA: false },
        numeracion: { prefijo: 'FAC', siguiente: 51, serie: 'A' },
        impuestos: { iva: 0.21, retenciones: {} },
        metodosPago: { efectivo: true, tarjeta: true, transferencia: true, mixto: true },
        general: { nombreNegocio: 'Multiservicios Las Acacias', logo: '/logo.jpeg', timezone: 'Europe/Madrid', moneda: 'EUR' }
    },
    
    init() {
        const saved = Storage.get('settings');
        if (saved) this.data = { ...this.data, ...saved };
        return this;
    },
    
    save() {
        Storage.set('settings', this.data);
    },
    
    getTarifas() {
        return this.data.tarifas;
    },
    
    getComision(tamano) {
        if (this.data.comisiones.tipo === 'fijo') return this.data.comisiones.valor;
        return this.data.comisiones.porTamaño?.[tamano] || this.data.comisiones.valor;
    },
    
    update(section, data) {
        this.data[section] = { ...this.data[section], ...data };
        this.save();
    }
};

// ============================================
// STORE: USUARIOS
// ============================================
Stores.users = {
    items: [],
    
    init() {
        const saved = Storage.get('users');
        if (saved) {
            this.items = saved;
        } else {
            this.seed();
        }
        return this;
    },
    
    seed() {
        this.items = [
            { id: 1, name: 'Administrador', email: 'admin@acacias.com', role: 'admin', active: true, password: 'admin123' },
            { id: 2, name: 'Operador', email: 'operador@acacias.com', role: 'operador', active: true, password: 'operador123' },
            { id: 3, name: 'Cajero', email: 'cajero@acacias.com', role: 'cajero', active: true, password: 'cajero123' },
            { id: 4, name: 'Auditor', email: 'auditor@acacias.com', role: 'auditor', active: true, password: 'auditor123' }
        ];
        this.save();
    },
    
    save() {
        Storage.set('users', this.items);
    },
    
    getAll() {
        return [...this.items];
    },
    
    getById(id) {
        return this.items.find(u => u.id == id);
    },
    
    create(data) {
        const user = { id: Date.now(), ...data };
        this.items.push(user);
        this.save();
        return user;
    },
    
    update(id, data) {
        const idx = this.items.findIndex(u => u.id == id);
        if (idx !== -1) {
            this.items[idx] = { ...this.items[idx], ...data };
            this.save();
        }
    },
    
    delete(id) {
        this.items = this.items.filter(u => u.id != id);
        this.save();
        return true;
    },
    
    toggleStatus(id) {
        const user = this.getById(id);
        if (user) {
            user.active = !user.active;
            this.save();
            return user.active;
        }
        return false;
    }
};

// ============================================
// INICIALIZAR TODOS LOS STORES
// ============================================
// Versión del set de datos demo: al cambiarla se regeneran los datos de prueba
const DEMO_VERSION = '2026-11-13-a';

function initStores() {
    if (Storage.get('demoVersion') !== DEMO_VERSION) {
        ['facturas', 'clientes', 'vehiculos', 'lavados', 'inventario', 'auditoria'].forEach((key) => Storage.remove(key));
        Storage.set('demoVersion', DEMO_VERSION);
    }

    Stores.auth.init();
    Stores.clientes.init();
    Stores.facturas.init();
    Stores.vehiculos.init();
    Stores.lavados.init();
    Stores.inventario.init();
    Stores.auditoria.init();
    Stores.settings.init();
    Stores.users.init();
    
    // Make available globally
    window.Stores = Stores;
    window.formatDate = formatDate;
    window.formatDateShort = formatDateShort;
    window.formatCurrency = formatCurrency;
    window.uid = uid;
}

// Arranque: restaura la sesión y siembra datos demo la primera vez
initStores();