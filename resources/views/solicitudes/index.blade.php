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
         <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fa-solid fa-house"></i> Inicio
            </a>
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
                <div class="fw-bold text-dark mb-1 small">
                    <span class="text-primary">{{ round($solicitud->dias, 0) }} DÍAS</span>
                </div>
                <div class="small text-muted fw-bold">{{ round($solicitud->horas, 0) }} HORAS</div>
            </td>

            <td class="text-center">
                @if($solicitud->estado == 'pendiente')
                    <span class="fw-bold text-warning small">
                        <i class="fas fa-spinner fa-spin me-1"></i>PENDIENTE
                    </span>
                @elseif($solicitud->estado == 'aprobado')
                    <span class="fw-bold text-success small">
                        <i class="fas fa-check-circle me-1"></i>APROBADO
                    </span>
                @else
                    <span class="fw-bold text-danger small">
                        <i class="fas fa-times-circle me-1"></i>RECHAZADO
                    </span>
                @endif
            </td>

            <td class="text-center">
                <div class="btn-group shadow-sm bg-white rounded">
                    {{-- VER --}}
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="verDetalles('{{ $solicitud->id }}')">
                        <i class="fas fa-eye"></i>
                    </button>

                    {{-- GESTIONAR --}}
                    @if(in_array(Auth::user()->rol->nombre, ['Administrador', 'Jefe']) && $solicitud->estado == 'pendiente')
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="abrirModalProcesar('{{ $solicitud->id }}')">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    @endif
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
</div>

{{-- MODAL DETALLES --}}
<div class="modal fade" id="verSolicitudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white border-0 no-print">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-alt me-2"></i>Formato FT-GTH-001</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalBodyContent"></div>
        </div>
    </div>
</div>

{{-- MODAL PROCESAR (Aprobar/Rechazar) --}}
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
                        <button type="button" onclick="enviarProcesar('aprobado')" class="btn btn-success fw-bold">
                            <i class="fas fa-check me-2"></i>APROBAR
                        </button>
                        <button type="button" onclick="enviarProcesar('rechazado')" class="btn btn-danger fw-bold">
                            <i class="fas fa-times me-2"></i>RECHAZAR
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
    const form = document.getElementById('formProcesar');
    
    // Ajustado para que coincida con el prefijo 'solicitudes' de tu web.php
    // La URL resultante será: /solicitudes/5/procesar
    form.action = '/solicitudes/' + id + '/procesar';
    
    var modal = new bootstrap.Modal(document.getElementById('procesarSolicitudModal'));
    modal.show();
}

function enviarProcesar(estado) {
    document.getElementById('inputEstado').value = estado;
    
    Swal.fire({
        title: '¿Confirmar ' + estado + '?',
        text: "Se registrará que tú aprobaste/rechazaste esta solicitud.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: estado === 'aprobado' ? '#198754' : '#dc3545',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formProcesar').submit();
        }
    });
}

</script>
@endsection