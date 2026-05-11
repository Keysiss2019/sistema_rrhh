<div class="modal fade" id="modalEditarProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="offcanvas-header modal-header  text-white mb-2 py-3">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Proyecto / Meta + Equipo</h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditarProyecto" method="POST">
                @csrf
                @method('PUT')
                
                <div id="hidden-designados-edit"></div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nombre del Proyecto</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Inicio</label>
                            <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Fin</label>
                            <input type="date" name="fecha_fin" id="edit_fecha_fin" class="form-control" required>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-primary mb-3">1) Gestionar Equipo</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Filtrar por Departamento</label>
                            <select id="edit-select-departamento" class="form-select" size="6" onchange="cargarEmpleadosDepto(this.value)">
                                <option value="" disabled selected>Elija un área...</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ strtoupper($depto->nombre) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">Colaboradores Disponibles</label>
                            <div id="edit-contenedor-empleados" class="border rounded p-2 px-4 bg-light" style="height: 155px; overflow-y:auto;">
                                <p class="text-center text-muted mt-3 italic">Seleccione un departamento para ver colaboradores</p>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-success mb-2">2) Tareas Asignadas</h6>
                    <button type="button" class="btn btn-sm btn-outline-success mb-2" onclick="agregarFilaManual()">
                        <i class="fas fa-plus"></i> Agregar tarea
                    </button>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" id="tablaTareasEdit">
                            <thead class="table-dark text-center">
                                <tr style="font-size: 0.85rem;">
                                    <th style="width: 30%;">Título</th>
                                    <th style="width: 25%;">Asignado a</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th style="width: 8%;">Peso %</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #054084;"> <i class="fa-solid fa-rotate me-2"></i>Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

//Mensaje de alerta 
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;">
  <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i> <span id="toast-mensaje"></span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  
    </div>
  </div>
</div>
<script>
var listaColaboradoresModal = [];

document.getElementById('formEditarProyecto').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const form = this;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // 1. Cerrar el modal de edición
            const modalEl = document.getElementById('modalEditarProyecto');
            const modalBS = bootstrap.Modal.getInstance(modalEl);
            if(modalBS) modalBS.hide();

            // 2. Mostrar la alerta estilo SweetAlert2
            Swal.fire({
                title: '¡Actualizado!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#0d6efd', // Color azul de tu sistema
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                // 3. Recargar la página al darle click a "Aceptar"
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else {
            // Alerta en caso de error
            Swal.fire({
                title: 'Error',
                text: data.message || 'No se pudo actualizar',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Ocurrió un fallo en el servidor', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// FUNCIÓN PARA PINTAR EL MENSAJE EN EL MODAL
function mostrarMensajeModal(mensaje, tipo = 'danger') {
    const contenedor = document.getElementById('msj-modal-edit');
    if (!contenedor) return;

    contenedor.innerHTML = `
        <div class="alert alert-${tipo} alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    
    document.querySelector('#modalEditarProyecto .modal-body').scrollTop = 0;
}


function editarProyecto(id, boton) {
    if (!boton) return;
    const originalHTML = boton.innerHTML;
    
    // Limpiar mensaje anterior si existe
    const contenedorMsj = document.getElementById('msj-modal-edit');
    if(contenedorMsj) contenedorMsj.innerHTML = '';

    boton.disabled = true;
    boton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(`/proyectos/${id}/edit`)
        .then(response => {
            if (!response.ok) return response.json().then(err => { throw err; });
            return response.json();
        })
        .then(data => {
            const p = data.proyecto;

            document.getElementById('edit_nombre').value = p.nombre || '';
            document.getElementById('edit_fecha_inicio').value = p.fecha_inicio || '';
            document.getElementById('edit_fecha_fin').value = p.fecha_fin || '';
            document.getElementById('formEditarProyecto').action = `/proyectos/${p.id}`;

            listaColaboradoresModal = data.colaboradores_actuales || [];

            const tablaBody = document.querySelector('#tablaTareasEdit tbody');
            tablaBody.innerHTML = '';
            if (p.tareas && Array.isArray(p.tareas)) {
                p.tareas.forEach((t, index) => {
                    tablaBody.insertAdjacentHTML('beforeend', generarFilaTareaEdit(index, t));
                });
            }

            sincronizarHiddenInputs();
            actualizarSelectsTareas();
            
            const modalEl = document.getElementById('modalEditarProyecto');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            
            // Si quieres confirmar que cargó:
            // mostrarMensajeModal("Datos cargados", "success");
        })
        .catch(error => {
            console.error("Error:", error);
            const msgError = error.error || error.message || "Error desconocido";
            
            // Abrimos el modal aunque falle para mostrar el error dentro
            const modalEl = document.getElementById('modalEditarProyecto');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            mostrarMensajeModal("Error al obtener datos: " + msgError, 'danger');
        })
        .finally(() => {
            boton.disabled = false;
            boton.innerHTML = originalHTML;
        });
}

function generarFilaTareaEdit(index, tarea = null) {
    const asignadoId = tarea ? String(tarea.asignado_user_id) : '';
    const options = listaColaboradoresModal.map(u => 
        `<option value="${u.id}" ${String(u.id) === asignadoId ? 'selected' : ''}>${u.nombre}</option>`
    ).join('');

    return `
        <tr>
            ${tarea ? `<input type="hidden" name="tareas[${index}][id]" value="${tarea.id}">` : ''}
            <td><input name="tareas[${index}][titulo]" class="form-control form-control-sm" value="${tarea ? tarea.titulo : ''}" required></td>
            <td>
                <select name="tareas[${index}][asignado_user_id]" class="form-select form-select-sm select-asignado" required>
                    <option value="">-- Seleccionar --</option>
                    ${options}
                </select>
            </td>
            <td><input type="date" name="tareas[${index}][fecha_inicio]" class="form-control form-control-sm" value="${tarea ? tarea.fecha_inicio : ''}" required></td>
            <td><input type="date" name="tareas[${index}][fecha_fin]" class="form-control form-control-sm" value="${tarea ? tarea.fecha_fin : ''}" required></td>
            <td><input type="number" name="tareas[${index}][peso]" class="form-control form-control-sm" value="${tarea ? tarea.peso : '10'}"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
}

function cargarEmpleadosDepto(deptoId) {
    const contenedor = document.getElementById('edit-contenedor-empleados');
    contenedor.innerHTML = 'Cargando...';

    fetch(`/departamentos/${deptoId}/empleados`)
        .then(response => response.json())
        .then(data => {
            contenedor.innerHTML = '';
            data.forEach(emp => {
                const idVincular = String(emp.user_id); 
                const estaEnEquipo = listaColaboradoresModal.some(c => String(c.id) === idVincular);
                const isChecked = estaEnEquipo ? 'checked' : '';

                contenedor.innerHTML += `
                    <div class="form-check form-switch mb-2 p-4 border-bottom">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               id="edit-emp-${idVincular}" value="${idVincular}" ${isChecked}
                               onclick="gestionarSeleccionColaborador('${idVincular}', '${emp.nombre} ${emp.apellido}')">
                        <label class="form-check-label fw-bold small" for="edit-emp-${idVincular}">
                            ${emp.nombre} ${emp.apellido}
                        </label>
                    </div>`;
            });
        });
}

function gestionarSeleccionColaborador(id, nombre) {
    id = String(id);
    const index = listaColaboradoresModal.findIndex(c => String(c.id) === id);
    if (index > -1) {
        listaColaboradoresModal.splice(index, 1);
    } else {
        listaColaboradoresModal.push({ id: id, nombre: nombre });
    }
    sincronizarHiddenInputs();
    actualizarSelectsTareas();
}

function sincronizarHiddenInputs() {
    const contHidden = document.getElementById('hidden-designados-edit');
    contHidden.innerHTML = '';
    listaColaboradoresModal.forEach(c => {
        contHidden.innerHTML += `<input type="hidden" name="designados[]" value="${c.id}">`;
    });
}

function actualizarSelectsTareas() {
    document.querySelectorAll('.select-asignado').forEach(select => {
        const valorActual = select.value;
        let options = '<option value="">-- Seleccionar --</option>';
        options += listaColaboradoresModal.map(u => 
            `<option value="${u.id}" ${String(u.id) === String(valorActual) ? 'selected' : ''}>${u.nombre}</option>`
        ).join('');
        select.innerHTML = options;
    });
}

function agregarFilaManual() {
    const tbody = document.querySelector('#tablaTareasEdit tbody');
    const index = tbody.querySelectorAll('tr').length;
    tbody.insertAdjacentHTML('beforeend', generarFilaTareaEdit(index));
}
</script>