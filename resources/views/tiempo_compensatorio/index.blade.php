@extends('layouts.app')

@section('content')

<div class="container py-4">
    {{-- ================= ENCABEZADO PRINCIPAL ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Título del módulo --}}
        <h3 class="fw-bold">
            <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>
            Gestión de Tiempo Compensatorio
        </h3>

        {{-- Obtiene el rol del usuario autenticado y valida si es Admin o Jefe --}}
        @php
            $rol = Auth::user()->rol->nombre ?? null;
            $esAdminOJefe = in_array($rol, ['Administrador', 'Jefe']);
        @endphp

        {{-- Botones de acción --}}
        <div class="d-flex gap-2">
            {{-- Verifica que exista un empleado seleccionado --}}
            @if(isset($empleado) && $empleado)

                {{-- Solo Admin o Jefe pueden cargar y ver horas extra --}}
                @if($esAdminOJefe)
                    {{-- Botón para abrir modal de carga de horas --}}
                    <button class="btn btn-dark btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAcumular">
                        <i class="fa-solid fa-plus me-1"></i> Cargar Horas
                    </button>

                    {{-- Botón para abrir modal de horas extra --}}
                    <button class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVerHorasExtra">
                        <i class="fa-solid fa-eye me-1"></i> Ver Horas Extra
                    </button>
                @endif
            @endif

            {{-- Botón para regresar al dashboard --}}
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fa-solid fa-house"></i> Inicio
            </a>
        </div>
    </div>

    {{-- ================= MENSAJE DE ÉXITO ================= --}}
    @if(session('success'))
        <div id="alert-success"
             class="alert alert-success alert-custom alert-dismissible fade show border-0 shadow-sm"
             role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ================= SELECTOR DE EMPLEADOS (Admin/Jefe) ================= --}}
    @if($esAdminOJefe)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-light rounded shadow-inner">
                {{-- Formulario para filtrar por empleado --}}
                <form action="{{ route('tiempo_compensatorio.index') }}" method="GET">
                    <label class="form-label fw-bold text-primary small text-uppercase">
                        Buscar Colaborador:
                    </label>

                    {{-- Select con envío automático --}}
                    <select name="empleado_id"
                            class="form-select select2 shadow-none"
                            onchange="this.form.submit()">
                        <option value="" {{ !request('empleado_id') ? 'selected' : '' }}>
                            -- Seleccione un colaborador --
                        </option>

                        {{-- Lista de empleados --}}
                        @foreach($empleados as $e)
                            <option value="{{ $e->id }}"
                                {{ (request('empleado_id') == $e->id || (isset($empleado) && $empleado->id == $e->id)) ? 'selected' : '' }}>
                                {{ $e->nombre }} {{ $e->apellido }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    @endif

    {{-- ================= CONTENIDO PRINCIPAL ================= --}}
    @if(isset($empleado) && $empleado)

        {{-- Obtiene el resumen de saldo del empleado --}}
        @php
            $resumen = \App\Models\SaldoTiempoCompensatorio::where('empleado_id', $empleado->id)->first();
        @endphp

        {{-- Tabla de movimientos --}}
        <div class="table-responsive card border-0 shadow-sm">
            <table class="tabla-personalizada w-100 mb-0">
                <thead>
                    <tr class="bg-light border-bottom">
                        <th class="ps-3">Fecha</th>
                        <th>Tipo</th>
                        <th>Movimiento</th>
                        <th class="table-primary text-center">Acumulado</th>
                        <th class="table-warning text-center">Usado</th>
                        <th class="table-success text-center">Pagado</th>
                        <th class="table-danger text-center">Debe</th>
                        <th class="text-center">Estado</th>
                        <th class="pe-3">Autorizado Por</th>
                    </tr>
                </thead>

                <tbody>
                {{-- Recorre los movimientos --}}
                @forelse($movimientos as $mov)
                    <tr class="border-bottom">
                        {{-- Fecha del movimiento --}}
                        <td class="ps-3 small text-muted">
                            {{ $mov->fecha_movimiento->format('d/m/Y H:i') }}
                        </td>

                        {{-- Tipo de movimiento --}}
                        <td>
                            <span class="badge {{ $mov->tipo_movimiento === 'libre' ? 'bg-warning text-dark' : 'bg-primary' }} text-uppercase"
                                  style="font-size: 0.7rem;">
                                {{ $mov->tipo_movimiento }}
                            </span>
                        </td>

                        {{-- Horas del movimiento --}}
                        <td class="fw-bold">{{ number_format($mov->horas, 2) }}h</td>

                        {{-- Resumen de saldos --}}
                        <td class="text-center small">{{ number_format($resumen->horas_acumuladas ?? 0, 2) }}</td>
                        <td class="text-center small">{{ number_format($resumen->horas_usadas ?? 0, 2) }}</td>
                        <td class="text-center small">{{ number_format($resumen->horas_pagadas ?? 0, 2) }}</td>
                        <td class="text-center text-danger fw-bold small">
                            {{ number_format($resumen->horas_debe ?? 0, 2) }}
                        </td>

                        {{-- Estado del movimiento --}}
                        <td class="text-center">
                            @if($mov->solicitud_id)
                                <span class="badge bg-success shadow-sm px-2">APROBADO</span>
                            @else
                                <span class="badge bg-secondary shadow-sm px-2">VALIDADO</span>
                            @endif
                        </td>

                        {{-- Usuario que autorizó --}}
                        <td class="pe-3 small fw-semibold text-secondary">
                            {{ $mov->autorizadoPor->empleado->nombre ?? 'Sistema' }}
                        </td>
                    </tr>
                @empty
                    {{-- Sin movimientos --}}
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted italic">
                            Sin movimientos registrados para este colaborador.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal para cargar horas --}}
        @include('tiempo_compensatorio.horas')

        {{-- Modal para visualizar horas extra --}}
        {{-- (estructura completa conservada, solo visualización) --}}
        {{-- ... --}}
    @else
        {{-- Mensaje cuando no hay empleado seleccionado --}}
        <div class="text-center py-5 bg-white rounded border shadow-sm">
            <i class="fa-solid fa-user-gear fa-3x text-light mb-3"></i>
            <p class="text-muted fs-5">
                Por favor, seleccione un colaborador para gestionar su tiempo compensatorio.
            </p>
        </div>
    @endif
</div>

{{-- ================= SCRIPT DE AUTO-CIERRE DE ALERTA ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alert = document.getElementById('alert-success');
        if (alert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 4000); // 4 segundos
        }
    });
</script>

@endsection

