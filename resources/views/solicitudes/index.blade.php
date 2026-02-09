{{-- Extiende el layout principal de la aplicación --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4 mb-5">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Historial de Solicitudes
            </h2>
            <p class="text-muted small mb-0">Gestión de permisos y formato oficial FT-GTH-001</p>
        </div>
        
    </div>

    {{-- NUEVA SECCIÓN: BUSCADOR (Mismo estilo que Empleados) --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
      <div class="card-body">
          <form action="{{ route('solicitudes.index') }}" method="GET" class="row g-3">
             {{-- Bajamos este a col-md-7 para que quepa todo --}}
              <div class="col-md-7">
                 <div class="input-group">
                     <span class="input-group-text bg-white border-end-0">
                         <i class="fa-solid fa-magnifying-glass text-muted"></i>
                      </span>
                     <input type="text" name="search" class="form-control border-start-0" 
                     placeholder="Buscar por nombre..." value="{{ request('search') }}">
                  </div>
              </div>
            
               {{-- Botón Buscar --}}
               <div class="col-md-2">
                  <button type="submit" class="btn btn-primary w-100 fw-bold">BUSCAR</button>
               </div>

               {{-- BOTÓN DE ESTADO DE CUENTA (Solo aparece en búsqueda exitosa) --}}
               @if(request('search') && $solicitudes->count() > 0)
                  @php $emp = $solicitudes->first()->empleado; @endphp
                   @if(strtolower($emp->tipo_contrato) == 'permanente')
                       <div class="col-md-3">
                           <button type="button" class="btn btn-warning w-100 fw-bold shadow-sm" 
                                onclick="verCalculoCicloGlobal('{{ $emp->id }}')"
                                 style="background-color: #f39c12; color: white; border: none;">
                               <i class="fas fa-calculator me-1"></i> ESTADO VACACIONES
                           </button>
                       </div>
                   @endif
               @endif
           </form>
        </div>
   </div>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 15px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle tabla-personalizada">
                    <thead>
                        <tr class="text-center">
                            <th class="text-start ps-4">Empleado / Cargo</th>
                            <th class="text-start">Tipo de Permiso</th>
                            <th class="text-start">Periodo</th>
                            <th>Días/Horas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary" style="font-size: 1.05rem;">
                                    {{ strtoupper($solicitud->empleado->nombre) }} {{ strtoupper($solicitud->empleado->apellido) }}
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-briefcase me-1"></i> 
                                    <span class="text-dark fw-bold">{{ strtoupper($solicitud->empleado->cargo) }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-white text-dark border shadow-sm px-2 py-1">
                                    {{ strtoupper(str_replace('_', ' ', $solicitud->tipo)) }}
                                </span>
                            </td>

                            <td>
                                <div class="small"><b class="text-success">INICIO:</b> {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }}</div>
                                <div class="small mt-1"><b class="text-danger">FIN:</b> {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}</div>
                            </td>

                            <td class="text-center">
                                <div class="fw-bold text-dark mb-1 small">{{ round($solicitud->dias, 0) }} DÍAS</div>
                                <div class="small text-muted fw-bold">{{ round($solicitud->horas, 0) }} HORAS</div>
                            </td>

                            <td class="text-center">
                                @if($solicitud->estado == 'pendiente')
                                    <span class="fw-bold text-warning small"><i class="fas fa-spinner fa-spin me-1"></i>PENDIENTE</span>
                                @elseif($solicitud->estado == 'aprobado')
                                    <span class="fw-bold text-success small"><i class="fas fa-check-circle me-1"></i>APROBADO</span>
                                @else
                                    <span class="fw-bold text-danger small"><i class="fas fa-times-circle me-1"></i>RECHAZADO</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="btn-group shadow-sm bg-white rounded">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="verDetalles('{{ $solicitud->id }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="button" 
                                        class="btn {{ $solicitud->estado == 'pendiente' ? 'btn-outline-warning' : 'btn-outline-secondary' }} btn-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar{{ $solicitud->id }}"
                                        {{ $solicitud->estado != 'pendiente' ? 'disabled' : '' }}>
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    @if(in_array(Auth::user()->rol->nombre, ['Administrador', 'Jefe']) && $solicitud->estado == 'pendiente')
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="abrirModalProcesar('{{ $solicitud->id }}')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                </div>

                                {{-- MODAL EDITAR --}}
                                <div class="modal fade" id="modalEditar{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg text-start">
                                        <div class="modal-content">
                                            <form action="{{ route('solicitudes.update', $solicitud->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header {{ $solicitud->estado == 'pendiente' ? 'bg-warning' : 'bg-light' }}">
                                                    <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Solicitud #{{ $solicitud->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold small">TIPO DE PERMISO</label>
                                                            <select name="tipo" class="form-select" required>
                                                                <option value="vacaciones" @selected($solicitud->tipo == 'vacaciones')>VACACIONES</option>
                                                                <option value="tiempo_compensatorio" @selected($solicitud->tipo == 'tiempo_compensatorio')>TIEMPO COMPENSATORIO</option>
                                                                <option value="sin_goce" @selected($solicitud->tipo == 'sin_goce')>SIN GOCE DE SUELDO</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label fw-bold small">FECHA INICIO</label>
                                                            <input type="date" name="fecha_inicio" class="form-control" value="{{ $solicitud->fecha_inicio }}" required>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label class="form-label fw-bold small">FECHA FIN</label>
                                                            <input type="date" name="fecha_fin" class="form-control" value="{{ $solicitud->fecha_fin }}" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label fw-bold small">MOTIVO / JUSTIFICACIÓN</label>
                                                            <textarea name="motivo" class="form-control" rows="3" required>{{ $solicitud->detalles }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">No hay registros encontrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- NUEVA SECCIÓN: PAGINACIÓN --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $solicitudes->appends(request()->query())->links() }}
    </div>
</div>

{{-- MODALES FUERA (Detalles y Procesar se mantienen igual) --}}
<div class="modal fade" id="verSolicitudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Formato FT-GTH-001</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBodyContent"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="procesarSolicitudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Gestionar Solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProcesar" method="POST">
                @csrf
                <div class="modal-body text-center p-4">
                    <p class="mb-4">¿Desea aprobar o rechazar esta solicitud?</p>
                    <input type="hidden" name="estado" id="inputEstado">
                    <div class="d-grid gap-2">
                        <button type="button" onclick="enviarProcesar('aprobado')" class="btn btn-success fw-bold">APROBAR</button>
                        <button type="button" onclick="enviarProcesar('rechazado')" class="btn btn-danger fw-bold">RECHAZAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS (Tus scripts originales se mantienen) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function verDetalles(id) {
        var content = document.getElementById('modalBodyContent');
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        var modal = new bootstrap.Modal(document.getElementById('verSolicitudModal'));
        modal.show();
        fetch('/solicitudes/' + id).then(r => r.text()).then(d => content.innerHTML = d);
    }

    function abrirModalProcesar(id) {
        document.getElementById('formProcesar').action = '/solicitudes/' + id + '/procesar';
        new bootstrap.Modal(document.getElementById('procesarSolicitudModal')).show();
    }

    function enviarProcesar(estado) {
        document.getElementById('inputEstado').value = estado;
        Swal.fire({
            title: '¿Confirmar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar'
        }).then((result) => { if (result.isConfirmed) document.getElementById('formProcesar').submit(); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modales = document.querySelectorAll('[id^="modalEditar"]');
        modales.forEach(modal => {
            const inicio = modal.querySelector('input[name="fecha_inicio"]');
            const fin = modal.querySelector('input[name="fecha_fin"]');
            const btn = modal.querySelector('button[type="submit"]');
            function validar() {
                if (inicio.value && fin.value && new Date(fin.value) < new Date(inicio.value)) {
                    fin.classList.add('is-invalid');
                    btn.disabled = true;
                } else {
                    fin.classList.remove('is-invalid');
                    btn.disabled = false;
                }
            }
            inicio.addEventListener('change', validar);
            fin.addEventListener('change', validar);
        });
    });

    function verCalculoCicloGlobal(empleadoId) {
    // Reutilizamos el modal de detalles pero con contenido de cálculo
    var content = document.getElementById('modalBodyContent'); 
    content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-warning"></div><p class="mt-2">Generando estado de cuenta...</p></div>';
    
    var modal = new bootstrap.Modal(document.getElementById('verSolicitudModal'));
    modal.show();

    fetch('/solicitudes/calculo-permanente/' + empleadoId)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        });
}
</script>
@endsection