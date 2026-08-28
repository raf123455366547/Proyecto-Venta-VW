<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Compra y Venta</title>
    <!-- Usamos Bootstrap mediante CDN para dar diseño rápido y limpio a los inputs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">

    <div class="container bg-white p-4 rounded shadow" style="max-width: 500px; margin-top: 40px;">
        <h2 class="text-center mb-4 text-primary">Registrar Nuevo Producto</h2>

        <!-- Mensaje de éxito al guardar en la Base de Datos -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alerta en caso de errores de validación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario conectado a la ruta POST de tu controlador -->
        <form action="{{ route('productos.store') }}" method="POST">
            <!-- Llave de seguridad obligatoria contra ataques CSRF en Laravel -->
            @csrf

            <!-- Campo: Nombre -->
            <div class="mb-3">
                <label for="nombre" class="form-label font-weight-bold">Nombre del Producto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Nombre del producto" value="{{ old('nombre') }}">
            </div>

            <!-- Campo: Precio -->
            <div class="mb-3">
                <label for="precio" class="form-label font-weight-bold">Precio</label>
                <input type="number" name="precio" id="precio" class="form-control" required placeholder="0000" value="{{ old('precio') }}">
            </div>

            <!-- Campo: Cantidad -->
            <div class="mb-3">
                <label for="cantidad" class="form-label font-weight-bold">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" required placeholder="0000" value="{{ old('cantidad') }}">
            </div>

            <!-- Botón de envío -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-block mt-2">Enviar </button>
            </div>
        </form>
    </div>

</body>
</html>