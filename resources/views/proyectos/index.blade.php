@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- 1. ENCABEZADO Y BOTÓN CREAR (Siempre arriba) --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-primary fw-bold">
        <i class="fas fa-briefcase me-2"></i>Gestión de Proyectos
    </h1>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoProyecto">
        <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nueva Meta / Proyecto
    </button>
</div>

    {{-- 2. FILA PRINCIPAL (GRID) --}}
    <div class="row">
        
        {{-- COLUMNA IZQUIERDA: Selector de Proyecto (4 de 12 espacios) --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="offcanvas-header modal-header text-white mb-2 py-3">
                    <h4  class="offcanvas-title fw-bold">Proyectos o Metas</h6>
                    @if(request('proyecto_id'))
                        <a href="{{ route('proyectos.index') }}" class="btn btn-sm btn-light text-Succes border">
                            <i class="fas fa-sync-alt"></i>Refrecar
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('proyectos.index') }}">
                        <label class="form-label small fw-bold text-muted">Seleccione un Proyecto o una Meta:</label>
                        <select name="proyecto_id" class="form-select form-select-lg border-primary mb-3" onchange="this.form.submit()">
                            <option value="">-- Seleccionar --</option>
                            @foreach($proyectos as $p)
                                <option value="{{ $p->id }}" {{ request('proyecto_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} ({{ $p->progreso }}%)
                                </option>
                            @endforeach
                        </select>
                    </form>
                    
                    {{-- Información extra del proyecto seleccionado --}}
                    @if($proyectoSeleccionado)
                        <hr>
                        <div class="small">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Estado Actual:</div>
                            <span class="badge bg-info text-dark">{{ $proyectoSeleccionado->estado }}</span>
                            
                            <div class="text-muted text-uppercase fw-bold mt-3" style="font-size: 0.7rem;">Progreso General:</div>
                            <div class="progress mt-1" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $proyectoSeleccionado->progreso }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Distribución de Tareas (8 de 12 espacios) --}}
        <div class="col-lg-8 mb-4">
            @if(request('proyecto_id') && $proyectoSeleccionado)
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">Equipo y Responsabilidades</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($diagramaAsignaciones as $asig)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 shadow-sm h-100" style="background-color: #f8f9fc; border-left: 4px solid {{ $asig['es_encargado'] ? '#4e73df' : '#1cc88a' }} !important;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="fw-bold text-dark">{{ $asig['usuario'] }}</div>
                                                <span class="badge {{ $asig['es_encargado'] ? 'bg-primary' : 'bg-success' }}" style="font-size: 0.65rem;">
                                                    {{ $asig['es_encargado'] ? 'Encargado' : 'Colaborador' }}
                                                </span>
                                            </div>
                                            
                                            <div class="responsabilidades mt-2">
                                                <ul class="list-unstyled mb-0">
                                                    @forelse($asig['tareas'] as $t)
                                                        <li class="small mb-1 d-flex align-items-center">
                                                            <i class="fas fa-check-circle {{ $t->completada ? 'text-success' : 'text-gray-300' }} me-2"></i>
                                                            <span class="{{ $t->completada ? 'text-decoration-line-through text-muted' : '' }}">
                                                                {{ $t->titulo }}
                                                            </span>
                                                        </li>
                                                    @empty
                                                        <li class="text-muted small italic">Sin tareas específicas.</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted w-100">No hay personal designado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                {{-- Placeholder cuando no hay nada seleccionado --}}
                <div class="card shadow h-100 border-0 d-flex align-items-center justify-content-center bg-light">
                    <div class="text-center py-5 opacity-50">
                        <i class="fas fa-arrow-left fa-3x mb-3 text-primary"></i>
                        <h5 class="text-gray-500">Seleccione un proyecto a la izquierda</h5>
                    </div>
                </div>
            @endif
        </div>

    </div> {{-- Fin Row --}}
</div>

    {{-- TABLA PRINCIPAL --}}
<div class="card shadow mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-custom-header text-center text-white">
                    <tr>
                        <th style="width: 30%;">Proyecto</th>
                        <th style="width: 25%;">Responsable</th>
                        <th style="width: 25%;">Progreso</th>
                        <th style="width: 20%;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center"> {{-- Centra todo el contenido de las filas --}}
                    @forelse($proyectos as $proyecto)
                        <tr style="vertical-align: middle;"> {{-- Alineación vertical al centro --}}
                            <td class="text-start ps-4"> {{-- El nombre del proyecto suele leerse mejor a la izquierda --}}
                                <strong>{{ $proyecto->nombre }}</strong>
                            </td>
                            <td>{{ $proyecto->usuario->usuario ?? 'N/A' }}</td>
                            <td>
                                <div class="px-3"> {{-- Margen interno para que la barra no toque los bordes --}}
                                    <div class="progress" style="height: 18px; border-radius: 10px; background-color: #eaecf4;">
                                        <div id="progreso-bar-{{ $proyecto->id }}" 
                                             class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                             style="width: {{ $proyecto->progreso }}%; font-weight: bold; font-size: 0.75rem;">
                                             {{ $proyecto->progreso }}%
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-white shadow-sm" 
                                        style="border: 2px solid #2ea9d9 !important; font-weight: bold; color: #2466a0;"
                                        onclick="abrirModalTareas({{ $proyecto->id }})">
                                    <i class="fas fa-tasks me-1"></i> Tareas
                                </button>

                                <button class="btn btn-outline-primary btn-sm btn-edit"
                                   onclick="editarProyecto({{ $proyecto->id }}, this)" 
                                   title="Editar Proyecto">
                                   <i class="fas fa-edit"></i>
                               </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No hay proyectos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    
    {{-- BOTONES DE PAGINACIÓN --}}
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Mostrando {{ $proyectos->firstItem() }} a {{ $proyectos->lastItem() }} de {{ $proyectos->total() }} proyectos
            </div>
            <div>
                {{ $proyectos->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="modalTareasProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div id="contenedor-alertas" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 350px;"></div>
        <div class="modal-content">
            <div class="modal-header modal-header text-white">
                <h5 class="modal-title">Validación de Tareas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="contenedor-tareas-ajax"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEscribirCorreccion" tabindex="-1" aria-hidden="true" style="z-index: 100000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning shadow-lg">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Solicitar Corrección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Explica al empleado qué debe mejorar en esta tarea:</p>
                <input type="hidden" id="tarea_id_correccion">
                <textarea id="texto_correccion" class="form-control" rows="4" placeholder="Ej: El documento adjunto no es el correcto..."></textarea>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" onclick="procesarEnvioCorreccion()" class="btn btn-warning btn-sm text-white px-4">
                    <i class="fas fa-paper-plane me-1"></i> Enviar a Corrección
                </button>
            </div>
        </div>
    </div>
</div>


@include('proyectos.crear')
@include('proyectos.edit-modal')

<script>
let miModalTareas;

function abrirModalTareas(id) {
    const contenedor = document.getElementById('contenedor-tareas-ajax');
    // Usamos clases de Bootstrap para el spinner
    contenedor.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-info" role="status"></div><p class="mt-2 text-muted">Cargando tareas...</p></div>';

    fetch(`/proyectos/${id}/get-tareas`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="list-group list-group-flush">'; // list-group-flush para que se vea más limpio en modales
            const usuarioActualId = {{ auth()->id() }};
            
            const rolActual = "{{ strtolower(trim(Auth::user()->rol)) }}"
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            const esJefe = (rolActual.includes('admin') || rolActual.includes('jefe'));

            data.tareas.forEach(t => {
                const estadoLimpio = t.estado 
                    ? t.estado.toLowerCase().trim().normalize("NFD").replace(/[\u0300-\u036f]/g, "") 
                    : '';

                let badgeColor = 'secondary';
                if (estadoLimpio === 'completado') badgeColor = 'success';
                else if (estadoLimpio.includes('revision')) badgeColor = 'warning';
                else if (estadoLimpio.includes('proceso')) badgeColor = 'info';
                else if (estadoLimpio.includes('rechazado')) badgeColor = 'danger';

                const urlArchivo = t.archivo_evidencia ? `{{ asset('storage') }}/${t.archivo_evidencia}` : null;

                // Nota de corrección con estilo Alert de Bootstrap
                let notaCorreccion = (t.observaciones_jefe && estadoLimpio === 'pendiente') 
                    ? `<div class="alert alert-warning py-2 px-3 mt-2 mb-2 small shadow-sm">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>Corrección:</strong> ${t.observaciones_jefe}
                       </div>` : '';

                let accionesJefe = '';
                if (esJefe && estadoLimpio.includes('revision')) {
                    accionesJefe = `
                        <div class="mt-3 p-3 bg-light border-start border-4 border-warning rounded shadow-sm">
                            <p class="small fw-bold mb-2 text-dark text-uppercase"><i class="fas fa-user-shield me-1"></i> Panel de Validación:</p>
                            <div class="d-flex gap-2">
                                <button type="button" onclick="validarTarea(${t.id})" class="btn btn-sm btn-success flex-fill fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Aprobar
                                </button>
                                <button type="button" onclick="solicitarCorreccion(${t.id})" class="btn btn-sm btn-outline-warning flex-fill fw-bold">
                                    <i class="fas fa-undo me-1"></i> Corregir
                                </button>
                            </div>
                        </div>`;
                }

                html += `
                <div class="list-group-item px-0 py-3 border-bottom shadow-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark">${t.titulo}</h6>
                        <span class="badge rounded-pill bg-${badgeColor} text-uppercase" style="font-size: 0.7rem;">${t.estado}</span>
                    </div>
                    
       <div class="mb-2">
        <small class="text-muted">
            <i class="fas fa-user-circle me-1"></i> Responsable: 
            <span class="text-primary fw-semibold">
                ${t.responsable ? t.responsable.name : '<span class="text-danger">Sin asignar</span>'}
            </span>
        </small>
    </div>

                    ${notaCorreccion}

                    <div class="mt-2">
                        <textarea id="obs-${t.id}" class="form-control form-control-sm mb-3 bg-white" 
                                  ${(estadoLimpio === 'completado') ? 'disabled' : ''} 
                                  placeholder="Escribe tus observaciones aquí...">${t.observaciones_empleado || ''}</textarea>
                        
                        <div class="d-md-flex justify-content-between align-items-center gap-2">
                            <div class="mb-2 mb-md-0">
                                ${urlArchivo ? `<a href="${urlArchivo}" target="_blank" class="btn btn-xs btn-outline-danger btn-sm shadow-sm"><i class="fas fa-file-pdf me-1"></i> Ver Evidencia</a>` : '<span class="text-muted small italic">Sin evidencia</span>'}
                            </div>

                            ${(t.asignado_user_id == usuarioActualId && estadoLimpio !== 'completado') ? `
                                <div class="input-group input-group-sm w-auto shadow-sm">
                                    <input type="file" id="file-${t.id}" class="form-control" style="max-width: 200px;">
                                    <button type="button" onclick="enviarARevision(event, ${t.id})" class="btn btn-primary px-3">
                                        <i class="fas fa-paper-plane me-1"></i> ${estadoLimpio.includes('revision') ? 'Actualizar' : 'Enviar'}
                                    </button>
                                </div>` : ''}
                        </div>
                        ${accionesJefe}
                    </div>
                </div>`;
            });
            contenedor.innerHTML = html || '<div class="text-center text-muted py-4"><i class="fas fa-tasks fa-3x mb-3 opacity-25"></i><p>No hay tareas asignadas.</p></div>';
        })
        .catch(error => {
            console.error('Error:', error);
            contenedor.innerHTML = '<div class="alert alert-danger d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i> Error al cargar las tareas del servidor.</div>';
        });

    const modalElement = document.getElementById('modalTareasProyecto');
    let modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modalInstance.show();
}

function enviarARevision(event, id) {
    if(event) event.preventDefault();

    const btn = event.currentTarget || event.target.closest('button');
    const fileInput = document.getElementById(`file-${id}`);
    const obsInput = document.getElementById(`obs-${id}`);
    const obs = obsInput ? obsInput.value : '';

    const formData = new FormData();
    formData.append('id', id);
    formData.append('observaciones', obs);
    if(fileInput.files[0]) formData.append('archivo', fileInput.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch("{{ route('tareas.revision') }}", {
        method: 'POST',
        body: formData,
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) return response.json().then(err => { throw err; });
        return response.json();
    })
    .then(data => {
        if(data.success) {
            Swal.fire({ title: '¡Listo!', text: 'Información actualizada.', icon: 'success' });
            abrirModalTareas(data.proyecto_id);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        Swal.fire({ title: 'Error', text: error.message || 'Error de base de datos.', icon: 'error' });
    });
}

function validarTarea(id) {
    Swal.fire({
        title: '<h5 class="fw-bold">¿Confirmar Validación?</h5>',
        text: "¿Estás seguro de que esta tarea está terminada correctamente?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, Aprobar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'btn btn-success px-4 mx-2',
            cancelButton: 'btn btn-secondary px-4 mx-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ url('/tareas/validar-jefe') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id: id, estado: 'Completado' })
            })
            .then(r => {
                if (!r.ok) throw new Error('Error en el servidor');
                return r.json();
            })
            .then(data => {
                if(data.success) {
                    const porcentaje = data.nuevo_progreso + '%';

                    // 1. Actualizar barra de la lista principal (si existe)
                    const barraLista = document.getElementById(`progreso-bar-${data.proyecto_id}`);
                    if (barraLista) {
                        barraLista.style.width = porcentaje;
                        barraLista.textContent = porcentaje;
                        if (data.nuevo_progreso >= 100) {
                            barraLista.classList.replace('bg-primary', 'bg-success');
                        }
                    }

                    // 2. Actualizar barra y texto del HEADER (la sección que preguntaste)
                    const barraHeader = document.getElementById(`barra-header-${data.proyecto_id}`);
                    const textoHeader = document.getElementById(`texto-header-${data.proyecto_id}`);
                    
                    if (barraHeader) {
                        barraHeader.style.width = porcentaje;
                    }
                    if (textoHeader) {
                        textoHeader.textContent = data.nuevo_progreso;
                    }

                    // 3. Actualizar el texto en el SELECT (opcional para coherencia visual)
                    const optionSelect = document.getElementById(`option-progreso-${data.proyecto_id}`);
                    if (optionSelect) {
                        const nombreOriginal = optionSelect.text.split('(')[0].trim();
                        optionSelect.text = `${nombreOriginal} (${porcentaje})`;
                    }

                    Swal.fire({
                        title: '¡Tarea Aprobada!',
                        text: 'El progreso del proyecto se ha actualizado en todo el tablero.',
                        icon: 'success',
                        confirmButtonClass: 'btn btn-primary'
                    }).then(() => {
                        if(typeof abrirModalTareas === 'function'){
                            abrirModalTareas(data.proyecto_id);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo validar la tarea en la base de datos.', 'error');
            });
        }
    });
}

// Agrega este estilo una sola vez en tu archivo para que SweetAlert siempre gane al modal
const style = document.createElement('style');
style.innerHTML = '.swal2-container { z-index: 99999 !important; }';
document.head.appendChild(style);

// Este código desactiva globalmente el bloqueo de escritura en modales de Bootstrap
// cuando un SweetAlert está presente.
$(document).on('show.bs.modal', '.modal', function() {
    const zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

// PARCHE DEFINITIVO PARA EL FOCO
$.fn.modal.Constructor.prototype._enforceFocus = function() {};

function solicitarCorreccion(id) {
    // 1. Guardamos el ID de la tarea en el input oculto del modal de corrección
    const inputId = document.getElementById('tarea_id_correccion');
    const inputText = document.getElementById('texto_correccion');
    
    if (inputId && inputText) {
        inputId.value = id;
        inputText.value = '';
        
        // 2. Abrimos el modal nativo de Bootstrap
        const el = document.getElementById('modalEscribirCorreccion');
        const modalCorreccion = bootstrap.Modal.getOrCreateInstance(el);
        modalCorreccion.show();
        
        // 3. Forzamos el foco en el textarea (solo una vez por apertura)
        el.addEventListener('shown.bs.modal', () => {
            inputText.focus();
        }, { once: true });
    }
}

function procesarEnvioCorreccion() {
    const id = document.getElementById('tarea_id_correccion').value;
    const observaciones = document.getElementById('texto_correccion').value;

    if (!observaciones.trim()) {
        Swal.fire({
            title: 'Atención',
            text: 'Debes escribir una observación para el empleado.',
            icon: 'warning',
            target: document.getElementById('modalEscribirCorreccion') // Para que el aviso salga SOBRE el modal
        });
        return;
    }

    // Cerramos el modal de escritura antes de procesar
    const modalEl = document.getElementById('modalEscribirCorreccion');
    const modalInst = bootstrap.Modal.getInstance(modalEl);
    if (modalInst) modalInst.hide();

    // Mostramos loading
    Swal.fire({ 
        title: 'Procesando...', 
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); } 
    });

    fetch("{{ url('/tareas/solicitar-correccion') }}", {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            id: id, 
            observaciones_jefe: observaciones 
        })
    })
    .then(r => {
        if (!r.ok) throw new Error('Error en el servidor');
        return r.json();
    })
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: '¡Tarea Devuelta!',
                text: 'Se ha solicitado la corrección exitosamente.',
                icon: 'success'
            }).then(() => {
                // Refrescamos la lista de tareas en el modal principal
                if (typeof abrirModalTareas === 'function') {
                    abrirModalTareas(data.proyecto_id);
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo completar la operación.', 'error');
    });
}


</script>

 <style>
    .table-custom-header th {
        background-color: #2d5ae1 !important;
        color: white !important;
        font-size: 1.1rem !important; /* Aumenta el tamaño aquí */
        font-weight: bold;
        text-transform: uppercase; /* Opcional: queda muy bien en sistemas administrativos */
        padding: 18px !important;
        vertical-align: middle;
    }
</style>
@endsection