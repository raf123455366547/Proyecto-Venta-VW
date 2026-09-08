@extends('layout')

@section('title', 'Inventario')

@section('content')

@if(!Auth::check())
    <script>window.location.href = "{{ route('login.index') }}";</script>
@endif

<div class="container my-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: #133a60;">Gestión de Inventario</h2>
        <button type="button" class="btn text-white color_azul" style="margin-right: 17%;" data-bs-toggle="modal" data-bs-target="#modalCrearProducto">
            + Agregar Artículo
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    
                    <button type="button" 
                            class="btn btn-danger btn-sm"
                            onclick="prepararEliminacion(this)"
                            data-id="{{ $producto->id }}"
                            data-nombre="{{ $producto->nombre }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalConfirmarEliminar">
                        Eliminar
                    </button>
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

<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5 mb-1">¿Estás seguro que quieres eliminar este artículo?</p>
                <strong id="nombreProductoEliminar" class="text-danger fs-6"></strong>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminarProducto" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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

        function prepararEliminacion(button) {
            let id = button.getAttribute('data-id');
            let nombre = button.getAttribute('data-nombre');

            document.getElementById('nombreProductoEliminar').textContent = `"${nombre}"`;
            document.getElementById('formEliminarProducto').action = `/producto/destroy/${id}`;
        }
    </script>
@endpush