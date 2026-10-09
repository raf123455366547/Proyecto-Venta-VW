<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventario - Compra y Venta')</title>

    <link rel="icon" href="{{ asset('assets/logo_ancgvw.ico.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .color_azul {
            background: #133a60 !important;
        }

        .color_white {
            background: #fcfdff !important;
        }

        html {
            scroll-behavior: smooth;
        }

        body{
            overflow-y: scroll;
            will-change: transform;
        }
    </style>
</head>

<body>

<header id="principal" class="p-3 text-bg-dark color_azul">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">

            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none me-4">
                <img src="{{ asset('assets/logo.ancgvw.png') }}" alt="Logo Inventario" width="40" height="40" class="me-2 object-fit-contain">
                <span class="fs-4 text-white font-weight-bold">ANCGVW</span>
            </a>

            <ul class="nav col-12 col-lg-auto mx-lg-auto mb-2 justify-content-center mb-md-0">
                @auth
                    @php
                        $userRoles = Auth::user()->roles->pluck('nombre')->map(fn($r) => trim(strtolower($r)))->toArray();
                    @endphp

                    {{-- Pestaña Productos (Disponible para Admin, Inventario e Inventario Ayudante) --}}
                    @if(array_intersect(['admin', 'inventario', 'inventario_ayudante', 'inventario ayudante'], $userRoles))
                    <li>
                        <a href="{{ route('productos.index') }}" class="nav-link px-3 text-white">
                            Productos
                        </a>
                    </li>
                    @endif

                    {{-- Pestaña Inventario (Disponible para Admin, Inventario e Inventario Ayudante) --}}
                    @if(array_intersect(['admin', 'inventario', 'inventario_ayudante', 'inventario ayudante'], $userRoles))
                    <li>
                        <a href="{{ route('inventario.index') }}" class="nav-link px-3 text-white">
                            Inventario
                        </a>
                    </li>
                    @endif

                    @if(array_intersect(['admin', 'ventas', 'ventas_ayudante', 'ayudante_ventas', 'ventas ayudante'], $userRoles))
                    <li>
                        <a href="{{ route('ventas.index') }}" class="nav-link px-3 text-white">
                            Ventas
                        </a>
                    </li>
                    @endif

                    {{-- Pestaña Usuarios (Exclusivo para Admin) --}}
                    @if(in_array('admin', $userRoles))
                    <li>
                        <a href="{{ route('usuarios.index') }}" class="nav-link px-3 text-white">
                            Usuarios
                        </a>
                    </li>
                    @endif
                @endauth
            </ul>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
                <input
                    type="search"
                    class="form-control form-control-dark text-bg-dark"
                    placeholder="Search..."
                    aria-label="Search"
                    style="background: #ffffff !important; color: #133a60 !important;">
            </form>

            <div class="text-end">

                @auth

                    <button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                        Mi perfil
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Cerrar Sesión
                        </button>
                    </form>

                @else

                    @if(!Route::is('login.index'))
                        <a href="{{ route('login.index') }}" class="btn btn-outline-light me-2 text-black color_white text-decoration-none">
                            Iniciar Sesión
                        </a>
                    @endif

                @endauth

            </div>

        </div>
    </div>
</header>

@auth
<div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white color_azul">
                <h5 class="modal-title " id="modalPerfilLabel">Mi perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_name" class="form-label">Nombre</label>
                        <input type="text" id="profile_name" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="profile_email" class="form-label">Correo electrónico</label>
                        <input type="email" id="profile_email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr>
                    <p class="mb-3">Deja los campos de contraseña vacíos si no quieres cambiarla.</p>
                    <div class="mb-3">
                        <label for="profile_current_password" class="form-label">Contraseña actual</label>
                        <input type="password" id="profile_current_password" name="current_password" class="form-control" autocomplete="current-password">
                        @error('current_password', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="profile_password" class="form-label">Nueva contraseña</label>
                        <input type="password" id="profile_password" name="password" class="form-control" autocomplete="new-password">
                        @error('password', 'profile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="profile_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" id="profile_password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white color_azul">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<main>
    @if (session('profile_success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('profile_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@auth
    @if ($errors->profile->any())
        <script>
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPerfil')).show();
        </script>
    @endif
@endauth
@stack('scripts')

</body>
</html>