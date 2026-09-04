<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
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
            <div class=" d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start "> 
                <a href="/" class="d-flex align-items-center mb-0 mb-lg-2 text-white text-decoration-none"> 
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

    <div class="container my-5">
       
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: #133a60;">Gestión de Inventario</h2>
            <button type="button" class="btn text-white color_azul" style="margin-right: 17%;" data-bs-toggle="modal" data-bs-target="#modalCrearProducto">
                + Agregar Artículo
            </button>
        </div>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $producto)
                <tr>
                    <td><strong>{{ $producto->id }}</strong></td>
                    <td>{{ $producto->nombre }}</td>
                    <td>${{ $producto->precio }}</td>
                    <td>{{ $producto->cantidad }}</td>
                    <td>
                        <button class="btn text-white btn-sm color_azul" 
                                onclick="cargarDatos(this)" 
                                data-id="{{ $producto->id }}"
                                data-nombre="{{ $producto->nombre }}"
                                data-precio="{{ $producto->precio }}"
                                data-cantidad="{{ $producto->cantidad }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEditarProducto">
                            Editar
                        </button>
                        
                        <form action="{{ route('producto.destroy', $producto->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalCrearProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white color_azul">
                    <h5 class="modal-title">Agregar Nuevo Artículo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white color_azul">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white color_azul">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarProducto" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" id="edit_precio" name="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" id="edit_cantidad" name="cantidad" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white color_azul">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function cargarDatos(button) {
            let id = button.getAttribute('data-id');
            let nombre = button.getAttribute('data-nombre');
            let precio = button.getAttribute('data-precio');
            let cantidad = button.getAttribute('data-cantidad');

            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_cantidad').value = cantidad;

            document.getElementById('formEditarProducto').action = `/producto/update/${id}`;
        }
    </script>
</body>
</html>