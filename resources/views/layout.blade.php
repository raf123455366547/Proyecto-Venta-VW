<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventario - Compra y Venta')</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/logo_ancgvw.ico.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
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
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"> 
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none"> 
                    <img src="{{ asset('assets/logo.ancgvw.png') }}" alt="Logo Inventario" width="40" height="40" class="me-2 object-fit-contain">        
                    <span class="fs-4 text-white font-weight-bold">ANCGVW</span>
                </a>
                
                <ul class="nav col-12 col-lg-auto mx-lg-auto mb-2 justify-content-center mb-md-0"> 
                    @auth
                    <li><a href="{{ route('productos.index') }}" class="nav-link px-3 text-white">Productos</a></li>
                    <li><a href="{{ route('inventario.index') }}" class="nav-link px-3 text-white">Inventario</a></li>
                    <li><a href="{{ route('usuarios.index') }}" class="nav-link px-3 text-white">Usuarios</a></li>
                    @endauth
                </ul>
                
                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search"> 
                    <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Search..." aria-label="Search" style="background: #ffffff !important;"> 
                </form>

                <div class="text-end">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                        </form>
                    @else
                        @if(!Route::is('login.index'))
                            <a href="{{ route('login.index') }}" class="btn btn-outline-light me-2 text-black color_white text-decoration-none">Login</a>
                        @endif
                    @endauth
                </div>
            </div> 
        </div> 
    </header>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>