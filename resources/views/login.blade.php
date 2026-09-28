@extends('layout')

@section('title', 'Iniciar Sesión - ANCGVW')

@section('content')
<div class="container mt-4 mb-4" style="max-width: 450px;">
    <div class="text-center mb-4">
        <h2 class="font-weight-bold" style="color: #133a60;">Iniciar Sesión</h2>
    </div>

    <!-- Alertas de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label font-weight-bold mb-0">Contraseña</label>
                <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color: #133a60;">¿Olvidaste tu contraseña?</a>
            </div>
            <input type="password" name="password" id="password" class="form-control mt-1" required placeholder="••••••••">
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn text-white color_azul fw-bold py-2">Ingresar</button>
        </div>

        <!-- Opción para ir al registro -->
        <div class="text-center mt-4 pt-2 border-top">
            <span class="text-muted small">¿Aún no tienes una cuenta?</span>
            <div class="mt-2">
                <a href="{{ route('register.index') }}" class="btn btn-outline-secondary btn-sm w-100 fw-bold py-2" style="color: #133a60; border-color: #133a60;">
                    Crear una cuenta nueva
                </a>
            </div>
        </div>
    </form>
</div>
@endsection