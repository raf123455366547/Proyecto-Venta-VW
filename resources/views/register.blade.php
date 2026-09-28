@extends('layout')

@section('title', 'Registro - ANCGVW')

@section('content')
<div class="container mt-0 mb-4" style="max-width: 520px;">
        <div class="text-center mb-4">
        <img src="{{ asset('assets/logo.ancgvw.png') }}" alt="Logo ANCGVW" class="img-fluid mb-2" style="max-height: 70px;">
        <h2 class="font-weight-bold" style="color: #133a60;">Crear Cuenta</h2>
    </div>

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

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label font-weight-bold">Nombre Completo</label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="Tu nombre completo" value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label font-weight-bold">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="usuario@correo.com" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label font-weight-bold">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>

        <!-- Requisitos de Contraseña -->
        <div class="card bg-light border-0 mb-3 p-3 text-muted small">
            <p class="fw-bold mb-1 text-dark">La contraseña debe incluir:</p>
            <ul class="mb-0 ps-3">
                <li>Mínimo 8 caracteres</li>
                <li>Al menos una letra mayúscula (A-Z)</li>
                <li>Al menos una letra minúscula (a-z)</li>
                <li>Al menos un número (0-9)</li>
                <li>Al menos un símbolo o carácter especial (@, $, !, %, *, ?, #, &)</li>
            </ul>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label font-weight-bold">Confirmar Contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••">
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn text-white color_azul mt-2 fw-bold">Registrarse</button>
        </div>

        <div class="text-center mt-3">
            <span class="text-muted">¿Ya tienes una cuenta?</span>
            <a href="{{ route('login.index') }}" class="fw-bold text-decoration-none ms-1" style="color: #133a60;">Inicia Sesión</a>
        </div>
    </form>
</div>
@endsection