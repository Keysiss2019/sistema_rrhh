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

    {{-- FORMULARIO ÚNICO DE FILTRADO --}}
    <form action="{{ route('solicitudes.index') }}" method="GET" id="form-filtros-unico">
        
        <div class="card mb-4 border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    {{-- BUSCAR POR NOMBRE --}}
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="Buscar por nombre..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- BOTONES PRINCIPALES --}}
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">BUSCAR</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary w-100 fw-bold">LIMPIAR</a>
                    </div>

                    {{-- BOTÓN ESTADO VACACIONES (Solo si hay búsqueda activa) --}}
                    @if(request('search') && $solicitudes->count() > 0)
                        @php $emp = $solicitudes->first()->empleado; @endphp
                        @if(strtolower($emp->tipo_contrato) == 'permanente')
                            <div class="col-md-4">
                                <button type="button" class="btn btn-warning w-100 fw-bold shadow-sm"
                                        onclick="verCalculoCicloGlobal('{{ $emp->id }}')"
                                        style="background-color:#f39c12;color:white;border:none;">
                                    <i class="fas fa-calculator me-1"></i> ESTADO VACACIONES
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- TABLA CON FILTRO DE PERIODO EN EL ENCABEZADO --}}
        <div class="card shadow-sm border-0 overflow-hidden" style="border-radius:15px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle tabla-personalizada mb-0">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start ps-4">Empleado / Cargo</th>
                                <th class="text-start">Tipo de Permiso</th>
                                <th class="text-start" style="width: 160px; vertical-align: middle;">
                                  <div class="d-flex align-items-center justify-content-start gap-2">
                                      <span class="fw-bold">PERIODO</span>
        
                                        {{-- Icono que dispara el calendario --}}
                                      <div class="position-relative" style="line-height: 1;">
                                           <i class="fa-solid fa-calendar-days text-white-50" 
                                             id="btn_calendario" 
                                              style="cursor: pointer; font-size: 1.1rem; transition: 0.3s;"
                                              onmouseover="this.classList.remove('text-white-50'); this.classList.add('text-warning');"
                                              onmouseout="this.classList.remove('text-warning'); this.classList.add('text-white-50');">
                                           </i>
            
                                          {{-- Input totalmente oculto --}}
                                          <input type="text" id="rango_fechas" name="rango" 
                                           value="{{ request('rango') }}"
                                            style="position: absolute; top: 0; left: 0; width: 0; height: 0; visibility: hidden; pointer-events: none;">
                                       </div>
                                 </div>
                               </th>
                                <th>Días/Horas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $solicitud)
                               <tr>
                                 <td class="ps-4">
                                      <div class="fw-bold text-primary">
                                          {{ strtoupper($solicitud->empleado->nombre) }} {{ strtoupper($solicitud->empleado->apellido) }}
                                      </div>
                                      <div class="small text-muted">
                                         <i class="fa-solid fa-briefcase me-1"></i> <b>{{ strtoupper($solicitud->empleado->cargo) }}</b>
                                      </div>
                                  </td>
                                  <td>
                                     <span class="badge bg-white text-dark border shadow-sm">
                                         {{ strtoupper(str_replace('_',' ',$solicitud->tipo)) }}
                                     </span>
                                  </td>
                                  <td>
                                     <div class="small"><b class="text-success">INICIO:</b> {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }}</div>
                                     <div class="small"><b class="text-danger">FIN:</b> {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}</div>
                                   </td>
                                   <td class="text-center">
                                     <div class="fw-bold">{{ round($solicitud->dias) }} DÍAS</div>
                                      <div class="small text-muted">{{ round($solicitud->horas) }} HORAS</div>
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
                                 <td colspan="6" class="text-center py-5 text-muted">No se encontraron registros.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

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

      // 1. Obtener la instancia del modal de Bootstrap y cerrarlo
     const modalElement = document.getElementById('procesarSolicitudModal');
     const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if(modalInstance) modalInstance.hide(); 

         // 2. Alerta de Confirmación
        Swal.fire({
            title: '¿Confirmar?',
            text: "Se registrará el estado: " + estado.toUpperCase(),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // 3. Mostrar mensaje de éxito temporal
                Swal.fire({
                    title: estado === 'aprobado' ? '¡Solicitud Aprobada!' : 'Solicitud Rechazada',
                    text: 'Procesando cambios en el sistema...',
                    icon: 'success',
                    timer: 2000, // Se cierra solo en 2 segundos
                    showConfirmButton: false,
                    willClose: () => {
                        // 4. Enviar el formulario al cerrarse el mensaje
                        document.getElementById('formProcesar').submit();
                    }
                });
            }
        });
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
     // El ID del div donde cae el contenido
     var content = document.getElementById('modalBodyContent'); 
     content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-warning"></div><p class="mt-2">Generando estado de cuenta...</p></div>';
    
     // El ID de tu modal (Cámbialo si tu modal se llama distinto)
     var modal = new bootstrap.Modal(document.getElementById('verSolicitudModal'));
     modal.show();

     // La ruta debe coincidir con web.php
     fetch('/solicitudes/calculo-permanente/' + empleadoId)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="alert alert-danger">Error al conectar con el servidor.</div>';
        });
    }
</script>

{{-- VALIDACIÓN FECHAS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Flatpickr en el input oculto
        const picker = flatpickr("#rango_fechas", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            // Esto evita que se cree un cuadro de texto adicional
            altInputClass: "d-none", 
            onClose: function(selectedDates, dateStr, instance) {
                // Si el usuario selecciona el rango (2 fechas) o limpia el filtro
                if (selectedDates.length === 2 || selectedDates.length === 0) {
                    document.getElementById('form-filtros-unico').submit();
                }
            }
        });

        // Al hacer clic en el icono, abrimos el calendario
        document.getElementById('btn_calendario').addEventListener('click', function() {
            picker.open();
        });
    });
</script>
@endsection