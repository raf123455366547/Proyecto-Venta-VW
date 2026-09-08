@extends('layout')

@section('title', 'Productos - Inventario')

@section('content')

@if(!Auth::check())
    <script>window.location.href = "{{ route('login.index') }}";</script>
@endif

<h2 class="text-center mt-5 mb-4 font-weight-bold" style="color: #133a60;">Nombre del Producto</h2>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
            <input type="number" step="0.01" name="precio" id="precio" class="form-control" required placeholder="0000" value="{{ old('precio') }}">
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label font-weight-bold">Cantidad</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" required placeholder="0000" value="{{ old('cantidad') }}">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-block mt-2 color_azul">Enviar</button>
        </div>
    </form>
</div>

@endsection