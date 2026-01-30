@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Tarjeta principal que contiene la bandeja de aprobaciones --}}
    <div class="card shadow mb-4">

        {{-- Encabezado de la tarjeta --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
            {{-- Título de la bandeja --}}
            <h6 class="m-0 font-weight-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Bandeja de Aprobaciones FT-GTH-002
            </h6>

            {{-- Contador de solicitudes pendientes --}}
            <span class="badge bg-light text-primary">
                {{ $pendientes->count() }} Pendientes
            </span>
        </div>

        {{-- Cuerpo de la tarjeta --}}
        <div class="card-body">
            <div class="table-responsive">
                {{-- Tabla de solicitudes pendientes --}}
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">

                    {{-- Encabezados de la tabla --}}
                    <thead class="table-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Lugar y Actividad</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Horas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    {{-- Cuerpo de la tabla --}}
                    <tbody>
                        @forelse($pendientes as $item)
                        <tr>
                            {{-- Información del empleado --}}
                            <td>
                                <strong>{{ $item->empleado->nombre }}</strong><br>
                                <small class="text-muted">{{ $item->codigo_formato }}</small>
                            </td>

                            {{-- Lugar y resumen de la actividad --}}
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->lugar }}</span><br>
                                <small>{{ Str::limit($item->detalle_actividad, 50) }}</small>
                            </td>

                            {{-- Fecha de inicio de la actividad --}}
                            <td>
                                {{ \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') }}
                            </td>

                            {{-- Fecha de fin de la actividad --}}
                            <td>
                                {{ \Carbon\Carbon::parse($item->fecha_fin)->format('d/m/Y') }}
                            </td>

                            {{-- Total de horas trabajadas --}}
                            <td class="text-center">
                                <span class="fw-bold text-primary">
                                    {{ $item->horas_trabajadas }}
                                </span>
                            </td>

                            {{-- Acciones disponibles --}}
                            <td>
                                <div class="d-flex gap-2">

                                    {{-- Botón para aprobar el registro --}}
                                    <form action="{{ route('horas_extras.validar', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Confirmar aprobación de horas?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="accion" value="aprobado">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>

                                    {{-- Botón para abrir el modal de rechazo --}}
                                    <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalRechazar{{ $item->id }}">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                {{-- Modal para rechazar el registro --}}
                                <div class="modal fade" id="modalRechazar{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('horas_extras.validar', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="accion" value="rechazado">

                                            <div class="modal-content">
                                                {{-- Encabezado del modal --}}
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Rechazar Registro</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                {{-- Cuerpo del modal --}}
                                                <div class="modal-body">
                                                    <label>Motivo del rechazo:</label>
                                                    <textarea name="observaciones_jefe"
                                                              class="form-control"
                                                              required
                                                              placeholder="Explique por qué se rechaza el registro..."></textarea>
                                                </div>

                                                {{-- Pie del modal --}}
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-danger">
                                                        Confirmar Rechazo
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        @empty
                        {{-- Mensaje cuando no hay solicitudes pendientes --}}
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-2"></i><br>
                                No hay solicitudes pendientes por aprobar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
