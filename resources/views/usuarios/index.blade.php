{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.app')

{{-- Inicio de la sección de contenido --}}
@section('content')
<!-- Contenedor principal de la sección -->
<div class="container-fluid roles-section py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">

        {{-- ================= MENSAJE DE ÉXITO ================= --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" id="success-alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>¡Atención!</strong> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tarjeta principal para usuarios -->
            <div class="card shadow-lg border-0">
                
                <!-- Header de la tarjeta -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0">
                        <i class="fa-solid fa-users me-2"></i> Gestión de Usuarios
                    </h4>
                    <div class="d-flex gap-2">

                        <!-- Botón para abrir offcanvas de nuevo usuario -->
                        <button class="btn btn-dark btn-sm shadow-sm fw-bold" 
                                type="button" 
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#offcanvasNuevoUsuario">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nuevo Usuario
                        </button>
                    </div>
                </div>

                <!-- Cuerpo de la tarjeta -->
                <div class="card-body px-4">

                    <!-- Filtro de búsqueda -->
                    <div class="row my-3">
                        <div class="col-md-5">
                            <form method="GET" action="{{ route('usuarios.index') }}">
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                    </span>
                                    <input type="text" name="buscar" class="form-control border-start-0 ps-0" 
                                           placeholder="Buscar usuario..." value="{{ request('buscar') }}">
                                    <button class="btn btn-primary" type="submit">Buscar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de usuarios -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">USUARIO</th>
                                    <th>EMPLEADO</th>
                                    <th>ROL</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center" style="width: 160px;">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $u)
                                <tr>
                                    <!-- Usuario -->
                                    <td class="ps-4 fw-bold text-primary">
                                        <i class="fa-solid fa-circle-user me-2"></i>{{ $u->usuario }}
                                    </td>

                                    <!-- Nombre del empleado asociado -->
                                    <td>{{ $u->empleado->nombre }}</td>

                                    <!-- Rol del usuario -->
                                    <td>
                                        <span class="badge rounded-pill bg-info text-dark px-3">
                                            {{ $u->rol->nombre }}
                                        </span>
                                    </td>

                                    <!-- Estado (activo/inactivo) -->
                                    <td class="text-center">
                                        <span class="badge {{ $u->estado == 'activo' ? 'bg-success' : 'bg-secondary' }} shadow-sm px-3 py-2">
                                            {{ ucfirst($u->estado) }}
                                        </span>
                                    </td>

                                    <!-- Acciones: editar, cambiar estado, eliminar -->
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <!-- Editar usuario -->
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                onclick="abrirEditar('{{ $u->id }}', '{{ $u->usuario }}', '{{ $u->empleado->nombre }} {{ $u->empleado->apellido }}', '{{ $u->role_id }}', '{{ $u->estado }}')"
                                                title="Editar Usuario">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>

                                            <!-- Cambiar estado -->
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="confirmarEstado('{{ $u->id }}', '{{ $u->usuario }}', '{{ $u->estado }}')"
                                                    title="Cambiar Estado">
                                                <i class="fa-solid fa-power-off"></i>
                                            </button>

                                            <!-- Eliminar usuario -->
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmarEliminar('{{ $u->id }}', '{{ $u->usuario }}')"
                                                    title="Eliminar Usuario">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Formularios ocultos para acciones POST/PUT -->
                                        <form id="estado-form-{{ $u->id }}" action="{{ route('usuarios.estado', $u->id) }}" method="POST" style="display: none;">
                                            @csrf 
                                            @method('PUT')
                                        </form>
                                        <form id="delete-form-{{ $u->id }}" action="{{ route('usuarios.destroy', $u->id) }}" method="POST" style="display: none;">
                                            @csrf 
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div> 
        </div>
    </div>
</div>

{{-- Incluimos offcanvas de creación y edición de usuarios --}}
@include('usuarios.create')
@include('usuarios.edit')

<!-- Script para abrir offcanvas de edición y llenar datos -->
<script>
function abrirEditar(id, usuario, empleado, roleId, estado) {
    // Actualizamos la acción del formulario con la ruta PUT
    const form = document.getElementById('formEditarUsuario');
    form.action = '/usuarios/' + id;

    // Llenamos los campos del offcanvas
    document.getElementById('edit_usuario').value = usuario;
    document.getElementById('edit_empleado').value = empleado;
    document.getElementById('edit_role_id').value = roleId;
    document.getElementById('edit_estado').value = estado;
    
    // Abrimos el offcanvas
    var myOffcanvas = document.getElementById('offcanvasEditarUsuario');
    var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
    bsOffcanvas.show();
}

// Mostrar u ocultar contraseña en input
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<!-- Script alternativo para otro input de contraseña -->
<script>
function togglePassword() {
    const passInput = document.getElementById('password');
    const icon = document.getElementById('password-icon');
    if (passInput.type === "password") {
        passInput.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passInput.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<!-- SweetAlert para confirmaciones -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Confirmación de eliminación
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Confirmación de cambio de estado
    function confirmarEstado(id, nombre, estado) {
        const accion = estado === 'activo' ? 'desactivar' : 'activar';
        Swal.fire({
            title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
            text: `El acceso de ${nombre} será modificado.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('estado-form-' + id).submit();
            }
        });
    }
</script>

<!-- Alerta de éxito -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Manejo de la alerta de éxito
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(successAlert);
                alertInstance.close();
            } else {
                successAlert.style.display = 'none';
            }
        }, 4000);
    } 
});
</script>
@endsection  {{-- Fin de la sección de contenido --}}
