<div class="modal fade" id="modalEditarProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="offcanvas-header modal-header text-white mb-2 py-3">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Proyecto / Meta + Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formEditarProyecto" method="POST">
                @csrf
                @method('PUT')
                
                <div id="hidden-designados-edit"></div>

                <div class="modal-body">
                    <div id="msj-modal-edit"></div>

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
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #054084;"> 
                        <i class="fa-solid fa-rotate me-2"></i>Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            const modalEl = document.getElementById('modalEditarProyecto');
            const modalBS = bootstrap.Modal.getInstance(modalEl);
            if(modalBS) modalBS.hide();

            Swal.fire({
                title: '¡Actualizado!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#0d6efd',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else {
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
    boton.disabled = true;
    
    fetch(`/proyectos/${id}/edit`)
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor: ' + response.status);
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);
            
            // Si esto imprime [] en la consola, el error está 100% en la consulta SQL del controlador
            if (data.tareas.length === 0) {
                alert("¡ALERTA! El servidor respondió, pero la lista de tareas llegó vacía. Revisa el log de Laravel.");
            } else {
                alert("Tareas recibidas: " + data.tareas.length);
                // Aquí iría tu lógica de renderizado...
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error: " + err.message);
        })
        .finally(() => boton.disabled = false);
}

function generarFilaTareaEdit(index, t) {
    return `<tr>
        <input type="hidden" name="tareas[${index}][id]" value="${t.id}">
        <td><input name="tareas[${index}][titulo]" class="form-control" value="${t.titulo}"></td>
        <td>
            <select name="tareas[${index}][asignado_user_id]" class="form-select select-asignado">
                ${listaColaboradoresModal.map(u => `<option value="${u.id}" ${u.id == t.asignado_user_id ? 'selected' : ''}>${u.nombre}</option>`).join('')}
            </select>
        </td>
        <td><input type="date" name="tareas[${index}][fecha_inicio]" class="form-control" value="${t.fecha_inicio}"></td>
        <td><input type="date" name="tareas[${index}][fecha_fin]" class="form-control" value="${t.fecha_fin}"></td>
        <td><input type="number" name="tareas[${index}][peso]" class="form-control" value="${t.peso}"></td>
        <td><button type="button" class="btn btn-danger" onclick="this.closest('tr').remove()">X</button></td>
    </tr>`;
}

function sincronizarHiddenInputs() {
    const cont = document.getElementById('hidden-designados-edit');
    cont.innerHTML = listaColaboradoresModal.map(c => `<input type="hidden" name="designados[]" value="${c.id}">`).join('');
}

function actualizarSelectsTareas() {
    document.querySelectorAll('.select-asignado').forEach(s => {
        let val = s.value;
        s.innerHTML = listaColaboradoresModal.map(u => `<option value="${u.id}" ${u.id == val ? 'selected' : ''}>${u.nombre}</option>`).join('');
    });
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


function agregarFilaManual() {
    const tbody = document.querySelector('#tablaTareasEdit tbody');
    const index = tbody.querySelectorAll('tr').length;
    tbody.insertAdjacentHTML('beforeend', generarFilaTareaEdit(index));
}
</script>