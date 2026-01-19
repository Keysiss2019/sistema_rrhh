{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.app')

{{-- Inicio de la sección content --}}
@section('content')

{{-- Contenedor principal de la vista --}}
<div class="container-fluid roles-section py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">

            {{-- Tarjeta principal --}}
            <div class="card shadow-lg border-0">

                {{-- Encabezado de la tarjeta --}}
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0">
                        {{-- Icono y título --}}
                        <i class="fa-solid fa-calendar-days me-2"></i> Políticas de Vacaciones
                    </h4>

                    {{-- Botones de acciones --}}
                    <div class="d-flex gap-2">
                        {{-- Botón volver a inicio --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm shadow-sm fw-bold">
                            <i class="fa-solid fa-house me-1"></i> Inicio
                        </a>

                        {{-- Botón para abrir offcanvas de nueva política --}}
                        <button class="btn btn-dark btn-sm shadow-sm fw-bold"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNuevaPolitica">
                            <i class="fa-solid fa-plus-circle me-1"></i> Nueva Política
                        </button>
                    </div>
                </div>

                {{-- Cuerpo de la tarjeta --}}
                <div class="card-body px-4">

                    {{-- Mensaje de éxito --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mt-3" 
                             role="alert" id="success-alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Tabla de políticas --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">TIPO DE CONTRATO</th>
                                    <th class="text-center">DÍAS ANUALES</th>
                                    <th class="text-center" style="width:150px;">ACCIONES</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- Recorrido de políticas --}}
                                @foreach($politicas as $politica)
                                <tr>
                                    {{-- Tipo de contrato --}}
                                    <td class="ps-4 fw-bold text-secondary">
                                        <i class="fa-solid fa-file-contract me-2 text-primary"></i>
                                        {{ ucfirst($politica->tipo_contrato) }}
                                    </td>

                                    {{-- Formulario para actualizar días --}}
                                    <td class="text-center">
                                        <form method="POST" 
                                              action="{{ route('politicas.update', $politica->id) }}" 
                                              id="form-update-{{ $politica->id }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="input-group input-group-sm mx-auto" style="max-width: 100px;">
                                                <input type="number" 
                                                       name="dias_anuales" 
                                                       value="{{ $politica->dias_anuales }}" 
                                                       class="form-control text-center border-2 border-primary-subtle fw-bold">
                                            </div>
                                        </form>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">

                                            {{-- Botón guardar cambios --}}
                                            <button type="submit" 
                                                    form="form-update-{{ $politica->id }}"
                                                    class="btn btn-outline-warning btn-sm" 
                                                    title="Guardar Cambios">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                            </button>

                                            {{-- Botón eliminar con SweetAlert --}}
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmarEliminacion('{{ $politica->id }}', '{{ $politica->tipo_contrato }}')"
                                                    title="Eliminar Política">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                            {{-- Formulario oculto para eliminación --}}
                                            <form id="delete-form-{{ $politica->id }}" 
                                                  action="{{ route('politicas.destroy', $politica->id) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
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

{{-- Offcanvas para registrar nueva política --}}
<div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="offcanvasNuevaPolitica">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title fw-bold">
            <i class="fa-solid fa-plus-circle me-2"></i> Registrar Nueva Política
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        {{-- Formulario de creación --}}
        <form method="POST" action="{{ route('politicas.store') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Tipo de Contrato</label>
                <input type="text" 
                       name="tipo_contrato" 
                       class="form-control form-control-lg border-2" 
                       placeholder="Ej: Permanente, Temporal..." 
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Días de Vacaciones Anuales</label>
                <input type="number" 
                       name="dias_anuales" 
                       class="form-control form-control-lg border-2" 
                       placeholder="Ej: 15" 
                       required>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow fw-bold">
                    <i class="fa-solid fa-save me-2"></i> Guardar Política
                </button>
                <button type="button" 
                        class="btn btn-secondary btn-lg fw-bold" 
                        data-bs-dismiss="offcanvas">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Confirmación de eliminación con SweetAlert
    function confirmarEliminacion(id, nombreContrato) {
        Swal.fire({
            title: '¿Eliminar política?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow',
                title: 'fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Ocultar alerta de éxito automáticamente
    document.addEventListener('DOMContentLoaded', function () {
        const alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }
    });
</script>

{{-- Fin de la sección content --}}
@endsection
