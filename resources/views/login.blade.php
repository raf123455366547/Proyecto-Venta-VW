@extends('layout')

@section('title', 'Login - Inventario')

@section('content')
<div class="container my-5" style="max-width: 500px;">
    <div class="text-center mb-4">
        <img src="{{ asset('assets/logo.ancgvw.png') }}" alt="Logo ANCGVW" class="img-fluid mb-2" style="max-height: 70px;">
        <h2 class="font-weight-bold" style="color: #133a60;">Iniciar Sesión</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="correo" class="form-label font-weight-bold">Correo Electrónico</label>
            <input type="email" name="correo" id="correo" class="form-control" required placeholder="usuario@correo.com" value="{{ old('correo') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label font-weight-bold">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-block mt-2 color_azul">Ingresar</button>
        </div>
    </form>
</div>
@endsection