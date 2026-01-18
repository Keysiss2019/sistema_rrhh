@extends('layouts.app')  <!-- =CONTENEDOR PRINCIPAL=========== -->

@section('content')   <!-- =Inicio de la sección de contenido=========== -->

<!-- ============================
 CONTENEDOR PRINCIPAL
============================ -->
<div class="container-fluid roles-section py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">

            <!-- ============================
             TARJETA PRINCIPAL
            ============================ -->
            <div class="card shadow-lg border-0">

                <!-- ===== HEADER ===== -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0">
                        <i class="fa-solid fa-shield-halved me-2"></i> Gestión de Roles
                    </h4>

                    <div class="d-flex gap-2">
                        <!-- Botón Inicio -->
                        <a href="{{ url('/') }}" class="btn btn-light btn-sm shadow-sm">
                            <i class="fa-solid fa-house"></i> Inicio
                        </a>

                        <!-- Botón Nuevo Rol (Offcanvas) -->
                        <button class="btn btn-dark btn-sm shadow-sm"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNuevoRol">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nuevo Rol
                        </button>
                    </div>
                </div>

                <!-- ===== BODY ===== -->
                <div class="card-body px-4">

                    <!-- ===== ALERTA DE ÉXITO ===== -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"
                             role="alert"
                             id="success-alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- ============================
                     TABLA DE ROLES
                    ============================ -->
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle custom-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:80px;">ID</th>
                                    <th>Nombre del Rol</th>
                                    <th>Descripción</th>
                                    <th class="text-center" style="width:150px;">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($roles as $rol)
                                <tr>

                                    <!-- ID -->
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            #{{ $rol->id }}
                                        </span>
                                    </td>

                                    <!-- Nombre -->
                                    <td class="fw-bold text-primary">
                                        {{ $rol->nombre }}
                                    </td>

                                    <!-- Descripción -->
                                    <td class="text-muted small">
                                        {{ $rol->descripcion ?: 'Sin descripción disponible.' }}
                                    </td>

                                    <!-- ===== ACCIONES ===== -->
                                    <td class="text-center">
                                        <div class="btn-group">

                                            <!-- BOTÓN EDITAR (ABRE MODAL) -->
                                            <button type="button"
                                                    class="btn btn-outline-warning btn-sm btn-edit"
                                                    data-id="{{ $rol->id }}"
                                                    data-nombre="{{ $rol->nombre }}"
                                                    data-descripcion="{{ $rol->descripcion }}"
                                                    title="Editar Rol">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <!-- BOTÓN ELIMINAR -->
                                            <form action="{{ route('roles.destroy', $rol->id) }}"
                                                  method="POST"
                                                  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-delete"
                                                        title="Eliminar Rol">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div> <!-- FIN CARD BODY -->
            </div> <!-- FIN CARD -->
        </div>
    </div>
</div>

<!-- ============================
 OFFCANVAS NUEVO ROL
============================ -->
<div class="offcanvas offcanvas-end border-0 shadow"
     tabindex="-1"
     id="offcanvasNuevoRol">

    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title fw-bold">
            <i class="fa-solid fa-plus-circle me-2"></i> Registrar Nuevo Rol
        </h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">
                    Nombre del Rol
                </label>
                <input type="text"
                       name="nombre"
                       class="form-control form-control-lg border-2"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">
                    Descripción
                </label>
                <textarea name="descripcion"
                          class="form-control border-2"
                          rows="4"></textarea>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit"
                        class="btn btn-primary btn-lg shadow">
                    <i class="fa-solid fa-save me-2"></i> Guardar Rol
                </button>
                <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="offcanvas">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================
 MODAL EDITAR ROL
============================ -->
<div class="modal fade"
     id="modalEditarRol"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Editar Rol
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEditarRol" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Nombre del Rol
                        </label>
                        <input type="text"
                               name="nombre"
                               id="edit_nombre"
                               class="form-control border-2"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Descripción
                        </label>
                        <textarea name="descripcion"
                                  id="edit_descripcion"
                                  class="form-control border-2"
                                  rows="4"></textarea>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit"
                                class="btn btn-warning fw-bold">
                            <i class="fa-solid fa-rotate me-2"></i>
                            Actualizar Cambios
                        </button>
                        <button type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- ============================
 SCRIPTS
============================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ===== OCULTAR ALERTA AUTOMÁTICA ===== */
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            new bootstrap.Alert(successAlert).close();
        }, 4000);
    }

    /* ===== CONFIRMACIÓN ELIMINAR ===== */
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('.delete-form');

            Swal.fire({
                title: '¿Eliminar rol?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    /* ===== MODAL EDITAR ===== */
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarRol'));
    const editForm = document.getElementById('formEditarRol');

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {

            document.getElementById('edit_nombre').value = this.dataset.nombre;
            document.getElementById('edit_descripcion').value = this.dataset.descripcion ?? '';

            // Ruta PUT dinámica
            editForm.action = `/roles/${this.dataset.id}`;

            editModal.show();
        });
    });

});
</script>

@endsection

