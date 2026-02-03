@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">

        <!-- HEADER DEL MÓDULO -->
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">
                <i class="fa-solid fa-building me-2"></i> Gestión de Departamentos
            </h4>

            <button class="btn btn-dark btn-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNuevoDepartamento">
                <i class="fa-solid fa-plus-circle"></i> Nuevo Departamento
            </button>
        </div>

        <!-- CUERPO -->
        <div class="card-body">

         <!-- Mensaje de exito -->
            @if(session('success'))
             <div id="success-alert" class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Departamento</th>
                            <th>Descripción</th>
                            <th>Jefe</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departamentos as $dep)
                        <tr>
                            <td>#{{ $dep->id }}</td>
                            <td class="fw-bold">{{ $dep->nombre }}</td>
                            <td>{{ $dep->descripcion }}</td>
                            <td>{{ $dep->jefe?->nombre ?? '' }} {{ $dep->jefe?->apellido ?? '' }}</td>
                            <td class="text-center">
                                <!-- Botón Editar -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-warning btn-edit-departamento"
                                        data-id="{{ $dep->id }}"
                                        data-nombre="{{ $dep->nombre }}"
                                        data-descripcion="{{ $dep->descripcion }}"
                                        data-jefe-id="{{ $dep->jefe?->id ?? '' }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <!-- Botón Eliminar -->
                                <form action="{{ route('departamentos.destroy', $dep->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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

<!-- OFFCANVAS NUEVO DEPARTAMENTO -->
<div class="offcanvas offcanvas-end" id="offcanvasNuevoDepartamento">
    <div class="offcanvas-header bg-primary text-white">
        <h5>Nuevo Departamento</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('departamentos.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Jefe del Departamento</label>
                <select name="jefe_empleado_id" class="form-select select2">
                    <option value="">-- Sin asignar --</option>
                    @foreach($empleados as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nombre }} {{ $emp->apellido }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary w-100">Guardar</button>
        </form>
    </div>
</div>

<!-- MODAL EDITAR DEPARTAMENTO -->
<div class="modal fade" id="modalEditarDepartamento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Editar Departamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEditarDepartamento" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Departamento</label>
                        <input type="text" name="nombre" id="edit_nombre_departamento" class="form-control border-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" id="edit_descripcion_departamento" class="form-control border-2" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Jefe del Departamento</label>
                        <select name="jefe_empleado_id" id="edit_jefe_departamento" class="form-select select2">
                            <option value="">-- Sin asignar --</option>
                            @foreach($empleados as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->nombre }} {{ $emp->apellido }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-warning fw-bold">
                            <i class="fa-solid fa-rotate me-2"></i> Actualizar Cambios
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Botón Editar
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarDepartamento'));
    const editForm = document.getElementById('formEditarDepartamento');

    document.querySelectorAll('.btn-edit-departamento').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_nombre_departamento').value = this.dataset.nombre;
            document.getElementById('edit_descripcion_departamento').value = this.dataset.descripcion;
            document.getElementById('edit_jefe_departamento').value = this.dataset.jefeId;

            // Inicializar Select2 con buscador en el modal
            $('#edit_jefe_departamento').select2({
                placeholder: "Busque por nombre",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalEditarDepartamento')
            });

            editForm.action = `/departamentos/${this.dataset.id}`;
            editModal.show();
        });
    });

    // Offcanvas Nuevo Departamento
    var offcanvas = document.getElementById('offcanvasNuevoDepartamento');
    offcanvas.addEventListener('shown.bs.offcanvas', function () {
        if (!$('.select2').hasClass('select2-hidden-accessible')) {
            $('.select2').select2({
                placeholder: "Seleccione un jefe...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#offcanvasNuevoDepartamento')
            });

            $('.select2').on('select2:open', function () {
                $('.select2-search__field').attr('placeholder', 'Busque por nombre');
            });
        }
    });
});
</script>

<script>
  // Mensaje de éxito
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('success-alert');
        if(alert){
            setTimeout(() => {
                // Aplicamos fade out usando clases de Bootstrap
                alert.classList.remove('show');
                alert.classList.add('hide');
            }, 3000); // Desaparece después de 3 segundos
        }
    });
</script>
@endsection

