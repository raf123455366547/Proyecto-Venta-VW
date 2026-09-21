@extends('layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: #133a60;">Módulo de Inventario</h2>
        
        <button type="button" class="btn text-white" style="background-color: #133a60;" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
            <i class="bi bi-box-seam me-1"></i> Registrar Producto
        </button>
    </div>

    {{-- Buscador de Productos --}}
    <form action="{{ route('inventario.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar producto por nombre..." value="{{ request('buscar') }}">
            <button class="btn text-white" type="submit" style="background-color: #133a60;">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead style="background-color: #133a60; color: white;">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                            <tr>
                                <td>{{ $producto->id }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>${{ number_format($producto->precio, 2) }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $producto->cantidad }}
                                    </span>
                                </td>
                                <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn text-white btn-sm me-1" style="background-color: #133a60;" data-bs-toggle="modal" data-bs-target="#modalEditarProducto{{ $producto->id }}">
                                        Editar
                                    </button>

                                    <!-- Modal Editar Producto -->
                                    <div class="modal fade" id="modalEditarProducto{{ $producto->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header text-white" style="background-color: #133a60;">
                                                    <h5 class="modal-title">Editar Producto</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('productos.update', $producto->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label for="nombre_{{ $producto->id }}" class="form-label">Nombre del Producto</label>
                                                            <input type="text" name="nombre" id="nombre_{{ $producto->id }}" class="form-control" value="{{ $producto->nombre }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="precio_{{ $producto->id }}" class="form-label">Precio Unitario</label>
                                                            <input type="number" step="0.01" name="precio" id="precio_{{ $producto->id }}" class="form-control" min="0" value="{{ $producto->precio }}" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="cantidad_{{ $producto->id }}" class="form-label">Cantidad</label>
                                                            <input type="number" name="cantidad" id="cantidad_{{ $producto->id }}" class="form-control" min="0" value="{{ $producto->cantidad }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn text-white" style="background-color: #133a60;">Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    @if(Auth::user()->roles->contains('nombre', 'admin') || Auth::user()->roles->contains('nombre', 'inventario'))
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalEliminarProducto{{ $producto->id }}">
                                            Eliminar
                                        </button>

                                        <!-- Modal Eliminar Producto -->
                                        <div class="modal fade" id="modalEliminarProducto{{ $producto->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header text-white bg-danger">
                                                        <h5 class="modal-title">Confirmar Eliminación</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                        ¿Estás seguro de que deseas eliminar el producto <strong>{{ $producto->nombre }}</strong>? Esta acción no se puede deshacer.
                                                        </div>
                                                        <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('producto.destroy', $producto->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar Producto</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No se encontraron productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Producto -->
<div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #133a60;">
                <h5 class="modal-title">Registrar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="">
                    </div>

                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio Unitario</label>
                        <input type="number" step="0.01" name="precio" id="precio" class="form-control" min="0" required placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad Inicial</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" min="0" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background-color: #133a60;">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection