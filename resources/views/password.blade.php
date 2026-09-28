@extends('layout')

@section('title', isset($token) ? 'Nueva Contraseña - ANCGVW' : 'Recuperar Contraseña - ANCGVW')

@section('content')
<div class="container mt-5 mb-4" style="max-width: 500px;">

    {{-- MODO 1: RESTABLECER CONTRASEÑA (cuando existe $token) --}}
    @if(isset($token))
        <div class="text-center mb-3">
            <h2 class="font-weight-bold" style="color: #133a60;">Crear Nueva Contraseña</h2>
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

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label font-weight-bold">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required value="{{ $email ?? old('email') }}" readonly>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label font-weight-bold">Nueva Contraseña</label>
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
                    <li>Al menos un carácter especial (@, $, !, %, *, ?, #, &)</li>
                </ul>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label font-weight-bold">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="••••••••">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn text-white color_azul fw-bold py-2">Guardar Nueva Contraseña</button>
            </div>
        </form>

    {{-- MODO 2: SOLICITAR ENLACE DE RECUPERACIÓN --}}
    @else
        <div class="text-center mb-4">
            <h2 class="font-weight-bold" style="color: #133a60;">Restablecer Contraseña</h2>
            <p class="text-muted small">Ingresa tu correo electrónico.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
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

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label font-weight-bold">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="usuario@correo.com" value="{{ old('email') }}">
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn text-white color_azul fw-bold py-2">Enviar Enlace de Recuperación</button>
            </div>

            <div class="text-center mt-4 border-top pt-3">
                <a href="{{ route('login.index') }}" class="text-decoration-none fw-bold small" style="color: #133a60;">
                    ← Volver al Inicio de Sesión
                </a>
            </div>
        </form>
    @endif

</div>
@endsection