@extends('layout')

@section('title', 'Usuarios')

@section('content')

@if(!Auth::check())
    <script>window.location.href = "{{ route('login.index') }}";</script>
@endif

<div class="container my-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: #133a60;">Gestión de Usuarios</h2>
        
        <button type="button" class="btn text-white color_azul" style="margin-right: 17%;" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
            + Agregar Usuario
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <table class="table align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td><strong>{{ $usuario->id }}</strong></td>
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->email }}</td>
                <td><span class="badge bg-secondary text-uppercase">{{ $usuario->role ?? 'ventas' }}</span></td>
                <td>
                    <button class="btn text-white btn-sm color_azul" 
                            onclick="cargarDatosUsuario(this)" 
                            data-id="{{ $usuario->id }}"
                            data-name="{{ $usuario->name }}"
                            data-email="{{ $usuario->email }}"
                            data-role="{{ $usuario->role ?? 'ventas' }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditarUsuario">
                        Editar
                    </button>

                    <button type="button" 
                            class="btn btn-danger btn-sm"
                            onclick="prepararEliminacionUsuario(this)"
                            data-id="{{ $usuario->id }}"
                            data-name="{{ $usuario->name }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#modalConfirmarEliminarUsuario">
                        Eliminar
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white color_azul">
                <h5 class="modal-title">Agregar Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol de Usuario</label>
                        <select name="role" class="form-select" required>
                            <option value="ventas" selected>Ventas</option>
                            <option value="inventario">Inventario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white color_azul">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white color_azul">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUsuario" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol de Usuario</label>
                        <select id="edit_role" name="role" class="form-select" required>
                            <option value="ventas">Ventas</option>
                            <option value="inventario">Inventario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="****">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white color_azul">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarEliminarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5 mb-1">¿Estás seguro que quieres eliminar este usuario?</p>
                <strong id="nombreUsuarioEliminar" class="text-danger fs-6"></strong>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        
                <form id="formEliminarUsuario" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        function cargarDatosUsuario(button) {
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');
            let email = button.getAttribute('data-email');
            let role = button.getAttribute('data-role');

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('formEditarUsuario').action = `/usuarios/update/${id}`;
        }

        function prepararEliminacionUsuario(button) {
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');

            document.getElementById('nombreUsuarioEliminar').textContent = `"${name}"`;
            document.getElementById('formEliminarUsuario').action = `/usuarios/destroy/${id}`;
        }
    </script>
@endpush