<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Compra y Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #principal {

        }
        .color_azul {
            background: #133a60 !important;
        }
        .color_white {
            background: #fcfdff !important;      
        }
    </style>
</head>

<body> 

    <header id="principal" class="p-3 text-bg-dark color_azul"> 
        <div class="container"> 
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none"> 
                    <img src="{{ asset('assets/logo.ancgvw.png') }}" alt="Logo Inventario" width="40" height="40" class="me-2">    
                    <span class="fs-4 text-white font-weight-bold">ANCGVW</span>
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0"> 
                    <li>
                        <a href="#" class="nav-link px-2 text-secondary"></a>
                    </li> 
                    <li>
                    </li> 
                    <li><a href="{{route('productos.index')}}" class="nav-link px-2 text-white">Productos</a></li>
                    <li><a href="{{route('inventario.index')}}" class="nav-link px-2 text-white">Inventario</a></li>
                </ul> 
                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search"> 
                    <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Search..." aria-label="Search" style="background: #ffffff !important;"> 
                </form>

                <div class="text-end"> 
                    <a href="{{ route('login.index') }}" class="btn btn-outline-light me-2 text-black color_white text-decoration-none">Login</a> 
                </div>
            </div> 
        </div>
    </header> 

    <h2 class="text-center mt-5 mb-4 font-weight-bold" style="color: #133a60;">Nombre del Producto</h2>
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('productos.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label font-weight-bold">Nombre del Producto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Nombre del producto" value="{{ old('nombre') }}">
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label font-weight-bold">Precio</label>
                <input type="number" name="precio" id="precio" class="form-control" required placeholder="0000" value="{{ old('precio') }}">
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label font-weight-bold">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" required placeholder="0000" value="{{ old('cantidad') }}">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-block mt-2 color_azul">Enviar </button>
            </div>
        </form>
    </div>
        

    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditar" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header color_azul text-white">
                        <h5 class="modal-title">Editar Producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nombre del Producto</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Precio</label>
                            <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Cantidad</label>
                            <input type="number" name="cantidad" id="edit_cantidad" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn color_azul text-white">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cargarDatos(button) {
            let id = button.getAttribute('data-id');
            let nombre = button.getAttribute('data-nombre');
            let precio = button.getAttribute('data-precio');
            let cantidad = button.getAttribute('data-cantidad');

            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_cantidad').value = cantidad;

            document.getElementById('formEditar').action = '/producto/update/' + id;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>