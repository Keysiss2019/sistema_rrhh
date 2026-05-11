<div class="row">
    <div class="col-md-5 border-end">
        <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-paper-plane me-2"></i>Nueva Asignación</h6>
        <form id="formEnviarEvaluacion">
            @csrf
            <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Colaborador:</label>
                <input type="text" class="form-control bg-light" value="{{ $empleado->nombre }} {{ $empleado->apellido }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-danger">Enviar a Jefe Evaluador:</label>
                <select name="jefe_id" id="jefe_id" class="form-select border-danger" required>
                    <option value="">-- Seleccione un Jefe --</option>
                    @foreach($jefes as $jefe)
                        <option value="{{ $jefe->id }}">{{ $jefe->usuario }}</option>
                    @endforeach
                </select>
            </div>

            <button type="button" onclick="enviarSolicitudEvaluacion()" class="btn btn-primary w-100 shadow-sm">
                <i class="fas fa-bell me-1"></i> Notificar al Jefe
            </button>
        </form>
    </div>

    <div class="col-md-7">
        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-list-check me-2"></i>Estado de Notificaciones</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Jefe</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $asig)
                    <tr>
                        <td>{{ $asig->jefe->usuario }}</td>
                        <td class="text-center">
                            @if($asig->completada)
                                <span class="badge bg-success">Completada</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente en Campanita</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center">No se han enviado solicitudes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>