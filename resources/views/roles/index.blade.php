@extends('layouts.app')  <!-- =CONTENEDOR PRINCIPAL=========== -->

@section('content')   <!-- =Inicio de la sección de contenido=========== -->

{{-- =========================================================
     CONTENEDOR PRINCIPAL
========================================================== --}}
<div class="container-fluid roles-section py-4">

    {{-- =========================================================
         FILA PRINCIPAL
    ========================================================== --}}
    <div class="row justify-content-center">

        {{-- =========================================================
             COLUMNA PRINCIPAL
        ========================================================== --}}
        <div class="col-md-11">

            {{-- =========================================================
                 TARJETA PRINCIPAL DE ROLES
            ========================================================== --}}
            <div class="card shadow-lg border-0">

                {{-- =========================================================
                     ENCABEZADO DE LA TARJETA
                ========================================================== --}}
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">

                    {{-- Título --}}
                    <h4 class="mb-0">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        Gestión de Roles
                    </h4>

                    {{-- Botón Nuevo Rol --}}
                    <div class="d-flex gap-2">

                        <button 
                            class="btn btn-primary btn-sm shadow-sm"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNuevoRol"
                        >

                            <i class="fa-solid fa-plus-circle me-1"></i>

                            Nuevo Rol
                        </button>
                    </div>
                </div>

                {{-- =========================================================
                     CUERPO DE LA TARJETA
                ========================================================== --}}
                <div class="card-body px-4">

                    {{-- =========================================================
                         TABLA RESPONSIVA
                    ========================================================== --}}
                    <div class="table-responsive mt-3">

                        {{-- Tabla de roles --}}
                        <table class="table table-bordered table-hover align-middle shadow-sm">

                            {{-- =========================================================
                                 ENCABEZADO DE TABLA
                            ========================================================== --}}
                            <thead>
                                <tr>

                                    <th class="text-center" style="width:80px;">
                                        ID
                                    </th>

                                    <th class="text-center">
                                        Nombre del Rol
                                    </th>

                                    <th class="text-center">
                                        Descripción
                                    </th>

                                    <th class="text-center" style="width:150px;">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            {{-- =========================================================
                                 CUERPO DE TABLA
                            ========================================================== --}}
                            <tbody>

                                {{-- Recorrido de roles --}}
                                @foreach($roles as $rol)

                                <tr>

                                    {{-- ID del rol --}}
                                    <td class="text-center">

                                        <span class="badge bg-light text-dark border">
                                            #{{ $rol->id }}
                                        </span>
                                    </td>

                                    {{-- Nombre del rol --}}
                                    <td class="ttext-muted small">
                                        {{ $rol->nombre }}
                                    </td>

                                    {{-- Descripción del rol --}}
                                    <td class="text-muted small">

                                        {{ $rol->descripcion ?: 'Sin descripción disponible.' }}
                                    </td>

                                    {{-- Botones de acciones --}}
                                    <td class="text-center">

                                        <div class="btn-group">

                                            {{-- =========================================================
                                                 BOTÓN EDITAR
                                            ========================================================== --}}
                                            <button 
                                                type="button"
                                                class="btn btn-outline-primary btn-sm btn-edit"
                                                data-id="{{ $rol->id }}"
                                                data-nombre="{{ $rol->nombre }}"
                                                data-descripcion="{{ $rol->descripcion }}"
                                                title="Editar Rol"
                                            >

                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            {{-- =========================================================
                                                 FORMULARIO ELIMINAR
                                            ========================================================== --}}
                                            <form 
                                                action="{{ route('roles.destroy', $rol->id) }}"
                                                method="POST"
                                                class="d-inline delete-form"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                {{-- Botón eliminar --}}
                                                <button 
                                                    type="button"
                                                    class="btn btn-outline-danger btn-sm btn-delete"
                                                    data-nombre="{{ $rol->nombre }}"
                                                    title="Eliminar Rol"
                                                >

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

                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     OFFCANVAS NUEVO ROL
========================================================== --}}
<div 
    class="offcanvas offcanvas-end border-0 shadow"
    tabindex="-1"
    id="offcanvasNuevoRol"
>

    {{-- =========================================================
         ENCABEZADO OFFCANVAS
    ========================================================== --}}
    <div class="offcanvas-header bg-primary text-white">

        <h5 class="offcanvas-title fw-bold">

            <i class="fa-solid fa-plus-circle me-2"></i>

            Registrar Nuevo Rol
        </h5>

        {{-- Botón cerrar --}}
        <button 
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
        ></button>
    </div>

    {{-- =========================================================
         CUERPO OFFCANVAS
    ========================================================== --}}
    <div class="offcanvas-body">

        {{-- Formulario crear rol --}}
        <form action="{{ route('roles.store') }}" method="POST">

            @csrf

            {{-- =========================================================
                 CAMPO NOMBRE
            ========================================================== --}}
            <div class="mb-4">

                <label class="form-label fw-bold text-secondary">
                    Nombre del Rol
                </label>

                <input 
                    type="text"
                    name="nombre"
                    class="form-control form-control-lg border-2"
                    required
                >
            </div>

            {{-- =========================================================
                 CAMPO DESCRIPCIÓN
            ========================================================== --}}
            <div class="mb-4">

                <label class="form-label fw-bold text-secondary">
                    Descripción
                </label>

                <textarea 
                    name="descripcion"
                    class="form-control border-2"
                    rows="4"
                ></textarea>
            </div>

            {{-- =========================================================
                 BOTONES DEL FORMULARIO
            ========================================================== --}}
            <div class="d-grid gap-2 mt-4">

                {{-- Guardar --}}
                <button 
                    type="submit"
                    class="btn btn-primary btn-lg shadow"
                >

                    <i class="fa-solid fa-save me-2"></i>

                    Guardar Rol
                </button>

                {{-- Cancelar --}}
                <button 
                    type="button"
                    class="btn btn-secondary btn-lg fw-bold"
                    data-bs-dismiss="offcanvas"
                >
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =========================================================
     MODAL EDITAR ROL
========================================================== --}}
<div 
    class="modal fade"
    id="modalEditarRol"
    tabindex="-1"
>

    {{-- Ventana modal --}}
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            {{-- =========================================================
                 ENCABEZADO MODAL
            ========================================================== --}}
            <div class="modal-header text-white">

                <h5 class="modal-title fw-bold">

                    <i class="fa-solid fa-pen-to-square me-2"></i>

                    Editar Rol
                </h5>

                {{-- Botón cerrar --}}
                <button 
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            {{-- =========================================================
                 CUERPO MODAL
            ========================================================== --}}
            <div class="modal-body">

                {{-- Formulario editar --}}
                <form id="formEditarRol" method="POST">

                    @csrf
                    @method('PUT')

                    {{-- Campo nombre --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Nombre del Rol
                        </label>

                        <input 
                            type="text"
                            name="nombre"
                            id="edit_nombre"
                            class="form-control border-2"
                            required
                        >
                    </div>

                    {{-- Campo descripción --}}
                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Descripción
                        </label>

                        <textarea 
                            name="descripcion"
                            id="edit_descripcion"
                            class="form-control border-2"
                            rows="4"
                        ></textarea>
                    </div>

                    {{-- Botones --}}
                    <div class="d-grid gap-2 mt-4">

                        {{-- Actualizar --}}
                        <button 
                            type="submit"
                            class="btn text-white rounded-pill px-4"
                            style="background-color: #054084;"
                        >

                            <i class="fa-solid fa-rotate me-2"></i>

                            Actualizar
                        </button>

                        {{-- Cancelar --}}
                        <button 
                            type="button"
                            class="btn btn-secondary btn-lg fw-bold"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================
     SCRIPT PRINCIPAL
========================================================== --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ALERTA DE ÉXITO
    |--------------------------------------------------------------------------
    */
    @if(session('success'))

        Swal.fire({

            title: '¡Logrado!',

            text: "{{ session('success') }}",

            icon: 'success',

            iconColor: '#a5dc86',

            showConfirmButton: false,

            timer: 3000,

            timerProgressBar: false,

            customClass: {

                popup: 'rounded-4 p-5 shadow-lg',

                title: 'fw-bold text-dark fs-2 mb-3',

                htmlContainer: 'text-muted fs-5'
            }
        });

    @endif

    /*
    |--------------------------------------------------------------------------
    | ALERTA DE ERROR
    |--------------------------------------------------------------------------
    */
    @if(session('error') || $errors->any())

        Swal.fire({

            title: '¡Error!',

            text: "{{ session('error') ?? 'Por favor, revise los campos del formulario.' }}",

            icon: 'error',

            confirmButtonColor: '#dc3545',

            customClass: {

                popup: 'rounded-4 shadow-lg'
            }
        });

    @endif

    /*
    |--------------------------------------------------------------------------
    | CONFIRMACIÓN PARA ELIMINAR
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.btn-delete').forEach(btn => {

        btn.addEventListener('click', function () {

            // Obtener formulario
            const form = this.closest('.delete-form');

            // Obtener nombre del rol
            const nombreRol = this.dataset.nombre;

            // Mostrar alerta
            Swal.fire({

                title: '¿Eliminar rol?',

                text: `Esta acción eliminará de forma permanente el rol "${nombreRol}". No se puede deshacer.`,

                icon: 'warning',

                iconColor: '#f8bb86',

                showCancelButton: true,

                confirmButtonColor: '#dc3545',

                cancelButtonColor: '#6c757d',

                confirmButtonText: 'Sí, eliminar',

                cancelButtonText: 'Cancelar',

                reverseButtons: true,

                customClass: {

                    popup: 'rounded-4 p-4 shadow-sm',

                    title: 'fw-bold text-secondary',

                    confirmButton: 'px-4 py-2 fw-bold me-2',

                    cancelButton: 'px-4 py-2 fw-bold'
                }

            }).then(result => {

                // Confirmar eliminación
                if (result.isConfirmed) {

                    form.submit();
                }
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | MODAL EDITAR ROL
    |--------------------------------------------------------------------------
    */

    // Crear instancia modal
    const editModal = new bootstrap.Modal(
        document.getElementById('modalEditarRol')
    );

    // Obtener formulario
    const editForm = document.getElementById('formEditarRol');

    /*
    |--------------------------------------------------------------------------
    | BOTONES EDITAR
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.btn-edit').forEach(btn => {

        btn.addEventListener('click', function () {

            // Asignar nombre
            document.getElementById('edit_nombre').value =
                this.dataset.nombre;

            // Asignar descripción
            document.getElementById('edit_descripcion').value =
                this.dataset.descripcion ?? '';

            /*
            |--------------------------------------------------------------------------
            | Asignar ruta dinámica PUT
            |--------------------------------------------------------------------------
            */
            editForm.action = `/roles/${this.dataset.id}`;

            // Mostrar modal
            editModal.show();
        });
    });

});
</script>

@endsection

