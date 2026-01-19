{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.app')

@section('content')

{{-- Cargamos el archivo CSS externo --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush

<div class="container-fluid mt-3 px-0">

    {{-- ===================== ENCABEZADO ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded border-bottom border-primary border-3">
        <h2 class="mb-0 text-primary fw-bold">
            <i class="fa-solid fa-users me-2"></i> Gestión de Empleados
        </h2>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-house"></i> Inicio
            </a>
            <button class="btn btn-dark px-4 shadow-sm fw-bold"
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
                               placeholder="Buscar por nombre, cargo, jefe o contrato..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MENSAJE DE ÉXITO ===================== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 mx-2" role="alert" id="success-alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ===================== TABLA DE EMPLEADOS ===================== --}}
    <div class="card shadow-sm border-0 mx-0 w-100 bg-transparent">
        <div class="card-body p-0">
            <div class="table-responsive px2">
                <table class="table align-middle tabla-personalizada" >
                    <thead>
                        <tr class="text-center">
                            <th style="width: 60px;">ID</th>
                            <th class="text-start">Empleado / Jefe</th>
                            <th class="text-start">Contacto (Email/Tel)</th>
                            <th class="text-start">Cargo y Departamento</th>
                            <th>Contrato</th>
                            <th class="text-start">Fechas</th>
                            <th>Estado</th>
                            <th>Doc</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $empleado)
                        <tr>
                            <td class="text-center text-muted fw-bold">#{{ $empleado->id }}</td>
                            
                            <td>
                                <div class="fw-bold text-primary" style="font-size: 1.05rem;">
                                    {{ strtoupper($empleado->nombre) }} {{ strtoupper($empleado->apellido) }}
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-user-tie me-1"></i> JEFE: 
                                    <span class="text-dark fw-bold">{{ $empleado->jefe_inmediato ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="small"><i class="fa-solid fa-envelope me-1 text-primary"></i> {{ $empleado->email }}</div>
                                @if($empleado->contacto)
                                    <div class="small mt-1 fw-bold text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $empleado->contacto }}</div>
                                @endif
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-1 small">{{ strtoupper($empleado->cargo) }}</div>
                                <div class="small text-muted fw-bold">{{ strtoupper($empleado->departamento) }}</div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-white text-dark border shadow-sm px-2 py-1">
                                    {{ strtoupper($empleado->tipo_contrato ?? 'N/A') }}
                                </span>
                            </td>

                            <td>
                                <div class="small"><b class="text-success">INGRESO:</b> {{ $empleado->fecha_ingreso ? \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') : '---' }}</div>
                                @if($empleado->fecha_baja)
                                    <div class="small mt-1"><b class="text-danger">BAJA:</b> {{ \Carbon\Carbon::parse($empleado->fecha_baja)->format('d/m/Y') }}</div>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="fw-bold {{ $empleado->estado == 'activo' ? 'text-success' : 'text-danger' }} small">
                                    {{ strtoupper($empleado->estado) }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($empleado->documentos && $empleado->documentos->count() > 0)
                                    <a href="{{ asset('storage/' . str_replace(['public/', 'storage/'], '', $empleado->documentos->first()->ruta_archivo)) }}" target="_blank">
                                        <i class="fa-solid fa-file-pdf text-danger fa-2xl"></i>
                                    </a>
                                @else
                                    <i class="fa-solid fa-minus text-light"></i>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group shadow-sm bg-white rounded">
                                    <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarEmpleado{{ $empleado->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('empleado.destroy', $empleado->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @include('empleado.edit')
                        @empty
                        <tr><td colspan="9" class="text-center py-5">No hay registros encontrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Offcanvas de creación --}}
@include('empleado.create')

{{-- ===================== SCRIPTS ===================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Alertas
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(successAlert).close();
        }, 4000);
    }

    // Confirmación eliminación
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('.delete-form');
            Swal.fire({
                title: '¿Eliminar empleado?',
                text: 'Esta acción borrará los datos permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>

@endsection
