{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.app')
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: none !important; /* Quita el icono default de bootstrap si prefieres */
    }
</style>
@section('content')

<div class="container-fluid mt-3 px-0">

    {{-- ===================== ENCABEZADO ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded">
        <h2 class="mb-0 text-primary fw-bold">
            <i class="fa-solid fa-users me-2"></i> Gestión de Empleados
        </h2>

        <div class="d-flex gap-2">
            <button class="btn btn-primary px-4 shadow-sm fw-bold"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNuevoEmpleado">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Empleado
            </button>
        </div>
    </div>

    {{-- ===================== FILTROS / BUSCADOR ===================== --}}
    <div class="card mb-4 border-0 shadow-sm mx-2">
        <div class="card-body">
            <form action="{{ route('empleado.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Buscar por nombre o cargo" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== TABLA DE EMPLEADOS ===================== --}}
    <div class="card shadow-sm border-0 mx-0 w-100 bg-transparent">
        <div class="card-body p-0">
            <div class="table-responsive px-2">
                <table class="table align-middle tabla-personalizada table-hover">
                    <thead class="table-primary text-white">
                        <tr class="text-center align-middle">
                            <th style="width: 120px;" class="text-uppercase fw-bold text-white bg-primary">Código/DNI</th>
                            <th class="text-start text-uppercase fw-bold text-white bg-primary">Empleado</th>
                            <th class="text-start text-uppercase fw-bold text-white bg-primary">Contacto</th>
                            <th class="text-start text-uppercase fw-bold text-white bg-primary">Cargo y Departamento</th>
                            <th class="text-uppercase fw-bold text-white bg-primary">Contrato</th>
                            <th class="text-start text-uppercase fw-bold text-white bg-primary">Fechas</th>
                            <th class="text-uppercase fw-bold text-white bg-primary">Estado</th>
                            <th class="text-uppercase fw-bold text-white bg-primary">Doc</th>
                            <th class="text-uppercase fw-bold text-white bg-primary">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $empleado)
                        <tr>
                            <td class="ps-3 text-center">
                                <div class="fw-bold text-primary mb-1" style="font-size: 0.9rem;">
                                    <i class="fa-solid fa-id-badge me-1"></i>{{ $empleado->codigo_empleado ?? 'S/C' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-fingerprint me-1"></i>{{ $empleado->dni ?? '0000-0000-00000' }}
                                </div>
                            </td>

                            <td>
                                <div class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                    {{ strtoupper($empleado->nombre) }} {{ strtoupper($empleado->apellido) }}
                                </div>
                            </td>

                            <td>
                                <div class="small text-muted mb-1">
                                    <i class="fa-solid fa-envelope me-1 text-primary"></i> {{ $empleado->email }}
                                </div>
                                <div class="small text-dark fw-medium">
                                    <i class="fa-solid fa-phone me-1 text-success"></i> {{ $empleado->contacto ?? 'N/A' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-1 text-uppercase" style="font-size: 0.85rem;">
                                    {{ $empleado->cargo }}
                                </div>

                                <div class="mb-1">
                                    @if($empleado->departamento)
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 fw-bold">
                                            <i class="fa-solid fa-building me-1"></i>
                                            {{ strtoupper($empleado->departamento->nombre) }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border px-2">SIN DEP.</span>
                                    @endif
                                </div>

                                <div class="text-muted small d-flex align-items-center">
                                    <i class="fa-solid fa-user-tie me-1 text-secondary"></i>
                                    <span>
                                        {{ $empleado->departamento?->jefeEmpleado 
                                            ? strtoupper($empleado->departamento->jefeEmpleado->nombre . ' ' . $empleado->departamento->jefeEmpleado->apellido) 
                                            : 'N/A' }}
                                    </span>
                                </div>

                                @if($empleado->departamentosComoJefe->count() > 0)
                                    <div class="mt-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.7rem;">
                                            <i class="fa-solid fa-star me-1"></i> LÍDER DE ÁREA
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-dark border shadow-sm px-2 py-1 fw-bold text-uppercase" style="font-size: 0.75rem;">
                                    {{ $empleado->tipo_contrato ?? 'N/A' }}
                                </span>
                            </td>

                            <td>
                                <div class="small"><b class="text-success">ING:</b> {{ $empleado->fecha_ingreso ? \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') : '---' }}</div>
                                @if($empleado->fecha_baja)
                                    <div class="small mt-1"><b class="text-danger">BAJ:</b> {{ \Carbon\Carbon::parse($empleado->fecha_baja)->format('d/m/Y') }}</div>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="fw-bold {{ $empleado->estado == 'activo' ? 'text-success' : 'text-danger' }} small text-uppercase">
                                    {{ $empleado->estado }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($empleado->documentos && $empleado->documentos->count() > 0)
                                    @php
                                        $primerDoc = $empleado->documentos->first();
                                        $extension = strtolower(pathinfo($primerDoc->ruta_archivo, PATHINFO_EXTENSION));
                                        
                                        // CORRECCIÓN DE RUTA: Limpiamos prefijos redundantes para evitar el error 404
                                        $pathLimpio = ltrim($primerDoc->ruta_archivo, '/');
                                        $pathLimpio = preg_replace('/^(public\/|storage\/)/i', '', $pathLimpio);
                                        $rutaWeb = asset('storage/' . $pathLimpio);
                                    @endphp
                                         
                                    <a href="{{ $rutaWeb }}" target="_blank" class="text-decoration-none" title="Ver documento: {{ $primerDoc->nombre_archivo }}">
                                        @if(in_array($extension, ['png', 'jpg', 'jpeg']))
                                            <span class="badge bg-danger-subtle text-danger border border-danger fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-file-image me-1"></i>{{ strtoupper($extension) }}
                                            </span>
                                        @elseif($extension == 'pdf')
                                            <span class="badge bg-warning-subtle text-warning border border-warning fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-file-pdf me-1"></i>PDF
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-file me-1"></i>DOC
                                            </span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-muted opacity-50">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group shadow-sm bg-white rounded border">
                                    <button type="button" class="btn btn-light btn-sm text-primary border-end"  data-bs-toggle="modal" data-bs-target="#modalEditarEmpleado{{ $empleado->id }}" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-light btn-sm text-secondary" 
                                            onclick="confirmarEstado('{{ $empleado->id }}', '{{ addslashes($empleado->nombre . ' ' . $empleado->apellido) }}', '{{ $empleado->estado }}')"
                                            title="Cambiar Estado">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>

                                    <form id="estado-form-{{ $empleado->id }}" action="{{ route('empleado.estado', $empleado->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @include('empleado.edit')
                        @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted">No se encontraron empleados en la base de datos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Offcanvas de creación --}}
@include('empleado.create')


{{-- ===================== SCRIPTS OPTIMIZADOS ===================== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Mostrar Alerta de ÉXITO (Si existe en sesión)
    @if(session('success'))
        Swal.fire({
            title: '¡Logrado!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#054084',
            timer: 3000
        });
    @endif

    // 2. Lógica para ERRORES (Apertura de modal + Alerta)
    @if(session('error_modal_id'))
        // A. Abrir el modal correspondiente
        var modalId = '#modalEditarEmpleado{{ session('error_modal_id') }}';
        var modalElement = document.querySelector(modalId);
        
        if (modalElement) {
            var myModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();
        }

        // B. Mostrar SweetAlert de error si hay un mensaje específico
        @if(session('sweet_error'))
            Swal.fire({
                title: '¡Error de validación!',
                text: "{{ session('sweet_error') }}",
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Entendido',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
    @endif
});

// Función de confirmación para cambiar de estado (Se mantiene igual, combinando con tu diseño)
function confirmarEstado(id, nombreCompleto, estado) {
    const accion = estado === 'activo' ? 'DESACTIVAR' : 'ACTIVAR';
    const color = estado === 'activo' ? '#dc3545' : '#198754';

    Swal.fire({
        title: `¿${accion} al empleado?`,
        text: `El estado de ${nombreCompleto} cambiará a ${estado === 'activo' ? 'inactivo' : 'activo'}.`,
        icon: 'warning',
        iconColor: '#f8bb86',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar',
        customClass: { 
            popup: 'rounded-4 p-4 shadow-sm',
            title: 'fw-bold text-secondary',
            confirmButton: 'px-4 py-2 fw-bold me-2',
            cancelButton: 'px-4 py-2 fw-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('estado-form-' + id).submit();
        }
    });
}
</script>

@endsection