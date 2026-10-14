@extends('layout')

@section('title', 'Punto de Venta - ANCGVW')

@section('content')
<style>
    .pos-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .btn-pos-item {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
        border: 1px solid #e2e8f0;
    }
    .btn-pos-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
    }
    .cart-table-container {
        height: 250px;
        overflow-y: auto;
    }
</style>

<div class="container-fluid px-3 my-2">
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form id="checkoutForm" action="{{ route('ventas.store') }}" method="POST" class="d-none">
        @csrf
    </form>

    <div class="row g-3">
        
        <!-- COLUMNA IZQUIERDA: Buscador, Carrito, Cliente y Totales -->
        <div class="col-lg-5 col-md-12">
            <div class="card pos-card border-0 p-3 bg-white">
                
                <!-- Buscadores Rápidos -->
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">🔍</span>
                            <input type="text" id="clienteInput" class="form-control bg-light border-start-0" placeholder="Cliente General" value="Cliente General">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">🔍</span>
                            <input type="text" id="searchInput" class="form-control bg-light border-start-0" placeholder="Producto" onkeydown="if(event.key==='Enter') buscarProducto()">
                        </div>
                    </div>
                </div>

                <!-- Datos del Cliente -->
                <div class="bg-light p-2 rounded-3 mb-2 border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" id="clienteNombreDisplay">Cliente General</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">Programa de Clientes / Venta Directa</small>
                        </div>
                        <span class="badge btn-ancgvw-style">Activo</span>
                    </div>
                    <div class="row text-center border-top mt-2 pt-1" style="font-size: 0.8rem;">
                        <div class="col-4">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">Saldo</span>
                            <strong class="text-dark">$0.00</strong>
                        </div>
                        <div class="col-4 border-start border-end">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">Puntos</span>
                            <strong class="text-dark">4,200</strong>
                        </div>
                        <div class="col-4">
                            <span class="text-muted d-block" style="font-size: 0.7rem;">Visitas</span>
                            <strong class="text-dark">19</strong>
                        </div>
                    </div>
                </div>

                <!-- Botones Rápidos de Acción -->
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <button class="btn btn-ancgvw-style btn-sm w-100 fw-semibold py-1" onclick="alert('Cotización generada')"> Cotización</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-ancgvw-style btn-sm w-100 fw-semibold py-1"> Ventas Previas</button>
                    </div>
                </div>

                <!-- Tabla del Carrito de Compras Dinámica -->
                <div class="cart-table-container border rounded-3 mb-2 bg-white">
                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-2">Nombre</th>
                                <th>Código</th>
                                <th class="text-center" style="width: 100px;">Cant.</th>
                                <th class="text-end pe-2">Precio</th>
                                <th class="text-center" style="width: 30px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <!-- Los productos se inyectan dinámicamente desde JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Totales y Botones Táctiles de Cobro -->
                <div class="border-top pt-2">
                    <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.8rem;">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.8rem;">
                        <span>IVA (16%):</span>
                        <span id="ivaDisplay">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-5 fw-bold text-dark">TOTAL</span>
                        <span class="fs-2 fw-bolder" style="color: #133a60;" id="totalDisplay">$0.00</span>
                    </div>

                    <div class="row g-2">
                        <div class="col-2">
                        </div>
                        <div class="col-4">
                            <button class="btn btn-danger w-100 py-2 fw-bold" onclick="vaciarCarrito()"> CANCELAR</button>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-success w-100 py-2 fw-bold" id="btnPagar" onclick="procesarPago()" disabled> PAGAR</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- COLUMNA DERECHA: Cuadrícula Táctil de Productos y Teclado POS -->
        <div class="col-lg-7 col-md-12">
            <div class="card pos-card border-0 p-3 bg-light">
                
                <!-- Categorías -->
                <div class="d-flex gap-2 overflow-auto pb-2 mb-2">
                    <button class="btn btn-ancgvw-style fw-bold px-3 btn-sm" onclick="filtrarCategoria('todas')">Todos los productos</button>
                </div>

                <!-- Cuadrícula de Productos Rápidos -->
                <div class="row g-2 mb-3" id="productosGrid">
                    <!-- Productos inyectados desde JS -->
                </div>

                <!-- Botones Operativos de Caja -->
                <!-- <div class="row g-2">
                    <!-- <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Cliente</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Ventas Recientes</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Pendientes</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Pedidos</button></div>

                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Entrada / Salida</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Buscar Stock</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Marcar Ent./Sal.</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Imprimir Ticket</button></div>

                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;" onclick="vaciarCarrito()"> Nueva Venta</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Guardar Venta</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Descuento %</button></div>
                    <div class="col-6 col-md-3"><button class="btn btn-ancgvw-style w-100 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.8rem;"> Facturar CFDI</button></div> -->
                <!-- </div> -->

            </div>
        </div>

    </div>
</div>

<script>
    const productosData = {{ Illuminate\Support\Js::from($productosDB) }};

    let cart = [];
    let categoriaActual = 'todas';

    // Renderizar Productos en la Grid
    function renderProductos() {
        const grid = document.getElementById('productosGrid');
        grid.innerHTML = '';

        const filtrados = categoriaActual === 'todas'
            ? productosData
            : productosData.filter(p => p.categoria === categoriaActual);

        filtrados.forEach(prod => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-2';
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'card bg-white btn-pos-item text-center p-2 rounded-3 h-100 w-100';
            card.disabled = prod.stock < 1;
            card.addEventListener('click', () => agregarAlCarrito(prod.id));

            const icon = document.createElement('div');
            icon.className = 'fs-4';
            icon.textContent = prod.icono;

            const name = document.createElement('div');
            name.className = 'fw-bold text-truncate';
            name.style.fontSize = '0.75rem';
            name.textContent = prod.nombre;

            const price = document.createElement('span');
            price.className = 'badge btn-ancgvw-style mt-1';
            price.textContent = `$${prod.precio.toFixed(2)}`;

            const stock = document.createElement('small');
            stock.className = 'text-muted mt-1';
            stock.textContent = `Disponibles: ${prod.stock}`;

            card.append(icon, name, price, stock);
            col.appendChild(card);
            grid.appendChild(col);
        });
    }

    // Agregar Producto al Carrito
    function agregarAlCarrito(id) {
        const producto = productosData.find(p => p.id === id);
        if (!producto) return;

        const existe = cart.find(item => item.id === id);
        if (existe) {
            if (existe.cantidad >= producto.stock) {
                alert('No hay más unidades disponibles de este producto.');
                return;
            }

            existe.cantidad++;
        } else {
            cart.push({ ...producto, cantidad: 1 });
        }
        renderCarrito();
    }

    // Cambiar Cantidad de un Ítem
    function cambiarCantidad(index, delta) {
        if (delta > 0 && cart[index].cantidad >= cart[index].stock) {
            alert('No hay más unidades disponibles de este producto.');
            return;
        }

        cart[index].cantidad += delta;
        if (cart[index].cantidad <= 0) {
            cart.splice(index, 1);
        }
        renderCarrito();
    }

    // Eliminar Ítem del Carrito
    function eliminarItem(index) {
        cart.splice(index, 1);
        renderCarrito();
    }

    // Vaciar Carrito Completo
    function vaciarCarrito() {
        cart = [];
        renderCarrito();
    }

    // Filtrar por Categoria
    function filtrarCategoria(cat) {
        categoriaActual = cat;
        renderProductos();
    }

    // Buscar por Teclado o Código de Barras
    function buscarProducto() {
        const input = document.getElementById('searchInput');
        const query = input.value.trim().toLowerCase();
        if (!query) return;

        const prod = productosData.find(p => p.codigo.toLowerCase() === query || p.nombre.toLowerCase().includes(query));
        if (prod) {
            agregarAlCarrito(prod.id);
            input.value = '';
        } else {
            alert('Producto no encontrado en inventario.');
        }
    }

    // Renderizar la Tabla del Carrito y Totales
    function renderCarrito() {
        const tbody = document.getElementById('cartTableBody');
        tbody.innerHTML = '';

        if (cart.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">El carrito está vacío. Haz clic en un producto para agregarlo.</td>
                </tr>
            `;
            document.getElementById('subtotalDisplay').innerText = '$0.00';
            document.getElementById('ivaDisplay').innerText = '$0.00';
            document.getElementById('totalDisplay').innerText = '$0.00';
            document.getElementById('btnPagar').disabled = true;
            return;
        }

        let totalGeneral = 0;

        cart.forEach((item, index) => {
            const subtotalItem = item.precio * item.cantidad;
            totalGeneral += subtotalItem;

            const tr = document.createElement('tr');
            const name = document.createElement('td');
            name.className = 'ps-2 fw-semibold';
            name.textContent = item.nombre;

            const code = document.createElement('td');
            code.className = 'text-muted small';
            code.textContent = item.codigo;

            const quantity = document.createElement('td');
            quantity.className = 'text-center';
            const quantityControls = document.createElement('div');
            quantityControls.className = 'input-group input-group-sm';

            const decrease = document.createElement('button');
            decrease.type = 'button';
            decrease.className = 'btn btn-outline-secondary btn-sm py-0 px-1';
            decrease.textContent = '-';
            decrease.addEventListener('click', () => cambiarCantidad(index, -1));

            const quantityValue = document.createElement('span');
            quantityValue.className = 'form-control form-control-sm text-center px-1 bg-white';
            quantityValue.textContent = item.cantidad;

            const increase = document.createElement('button');
            increase.type = 'button';
            increase.className = 'btn btn-outline-secondary btn-sm py-0 px-1';
            increase.textContent = '+';
            increase.addEventListener('click', () => cambiarCantidad(index, 1));

            quantityControls.append(decrease, quantityValue, increase);
            quantity.appendChild(quantityControls);

            const subtotal = document.createElement('td');
            subtotal.className = 'text-end pe-2 fw-bold';
            subtotal.textContent = `$${subtotalItem.toFixed(2)}`;

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-link text-danger p-0 border-0';
            remove.textContent = '❌';
            remove.setAttribute('aria-label', `Eliminar ${item.nombre}`);
            remove.addEventListener('click', () => eliminarItem(index));

            const actions = document.createElement('td');
            actions.className = 'text-center';
            actions.appendChild(remove);

            tr.append(name, code, quantity, subtotal, actions);
            tbody.appendChild(tr);
        });

        const subtotal = totalGeneral / 1.16;
        const iva = totalGeneral - subtotal;

        document.getElementById('subtotalDisplay').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('ivaDisplay').innerText = '$' + iva.toFixed(2);
        document.getElementById('totalDisplay').innerText = '$' + totalGeneral.toFixed(2);
        document.getElementById('btnPagar').disabled = false;
    }

    // Procesar Venta / Pago
    function procesarPago() {
        if (cart.length === 0) return;

        const form = document.getElementById('checkoutForm');
        form.querySelectorAll('input[name^="items["]').forEach(input => input.remove());

        cart.forEach((item, index) => {
            const productId = document.createElement('input');
            productId.type = 'hidden';
            productId.name = `items[${index}][producto_id]`;
            productId.value = item.id;

            const quantity = document.createElement('input');
            quantity.type = 'hidden';
            quantity.name = `items[${index}][cantidad]`;
            quantity.value = item.cantidad;

            form.append(productId, quantity);
        });

        document.getElementById('btnPagar').disabled = true;
        form.submit();
    }

    // Inicializar al cargar la vista
    document.addEventListener('DOMContentLoaded', () => {
        renderProductos();
        renderCarrito();

        // Actualizar nombre del cliente en tiempo real
        document.getElementById('clienteInput').addEventListener('input', (e) => {
            document.getElementById('clienteNombreDisplay').innerText = e.target.value.trim() || 'Cliente General';
        });
    });
</script>
@endsection