@extends('layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: #133a60;">Módulo de Ventas</h2>
        
        <button type="button" class="btn text-white" style="background-color: #133a60;" data-bs-toggle="modal" data-bs-target="#modalNuevaVenta">
            <i class="bi bi-cart-plus me-1"></i> Registrar Nueva Venta
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead style="background-color: #133a60; color: white;">
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Vendedor</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>{{ $venta->producto?->nombre ?? 'Producto no encontrado' }}</td>
                                <td>{{ $venta->usuario?->name ?? 'Usuario N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ $venta->cantidad }}</span></td>
                                <td>${{ number_format($venta->precio_unitario, 2) }}</td>
                                <td class="fw-bold text-success">${{ number_format($venta->total, 2) }}</td>
                                <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay ventas registradas en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar Venta -->
<div class="modal fade" id="modalNuevaVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #133a60;">
                <h5 class="modal-title">Registrar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ventas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="producto_id" class="form-label">Seleccionar Producto</label>
                        <select name="producto_id" id="producto_id" class="form-select" required>
                            <option value="" selected disabled>-- Seleccione un producto --</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}">
                                    {{ $producto->nombre }} (Stock: {{ $producto->cantidad }} | Precio: ${{ number_format($producto->precio, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad a Vender</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white" style="background-color: #133a60;">Confirmar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection