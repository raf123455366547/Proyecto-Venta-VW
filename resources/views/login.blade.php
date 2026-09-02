<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Compra y Venta</title>
    <!-- Usamos Bootstrap mediante CDN para dar diseño rápido y limpio a los inputs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #principal {

        }
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
                <img src="{{ asset('asests/logo.ancgvw.png') }}" alt="Logo Inventario" width="40" height="40" class="me-2">    
                <span class="fs-4 text-white font-weight-bold">ANCGVW</span>
            </a>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0"> 
                <li><a href="#" class="nav-link px-2 text-secondary">Home</a></li> 
                <li></li> 
                <li><a href="{{ route('productos.index') }}" class="nav-link px-2 text-white">Productos</a></li>
                <li><a href="#" class="nav-link px-2 text-white">About</a></li> 
            </ul> 
            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search"> 
                <input type="search" class="form-control form-control-dark text-bg-dark" placeholder="Search..." aria-label="Search" style="background: #ffffff !important;"> 
            </form>
            <div class="text-end"> 
                <button type="button" class="btn btn-outline-light me-2 text-black color_white">Login</button> 
                <button type="button" class="btn btn-outline-light me-2 text-black color_white">Sign-up</button> 
            </div> 
        </div> 
    </div> 
</header> 
    
<h2 class="text-center mt-5 mb-4 font-weight-bold" style="color: #133a60;">Iniciar Sesión</h2>

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

</body>
</html>