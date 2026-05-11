<div class="modal fade" id="modalNuevoProyecto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-rocket me-2"></i>Nueva Meta / Proyecto + Tareas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('proyectos.store') }}" method="POST" id="formProyecto">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nombre del Proyecto / Meta</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-primary mb-3">1) Equipo del proyecto</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Departamento</label>
                            <select id="select-departamento" class="form-select" size="8">
                                <option value="" disabled selected>Elija un área...</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id }}">{{ strtoupper($depto->nombre) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Colaboradores</label>
                            <div id="contenedor-empleados" class="border rounded p-2" style="height: 200px; overflow-y:auto; background:#f8f9fa;"></div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-success mb-2">2) Tareas del proyecto (esto es lo que luego validarás)</h6>
                    <button type="button" id="btnAgregarTarea" class="btn btn-sm btn-outline-success mb-2">
                        <i class="fas fa-plus"></i> Agregar tarea
                    </button>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="tablaTareas">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Asignado a</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Peso %</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <small class="text-muted">Tip: al menos 1 tarea por colaborador para medir desempeño real.</small>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Proyecto + Tareas</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const departamentos = @json($departamentos->load('empleados'));
    const selectDepto = document.getElementById('select-departamento');
    const contenedor = document.getElementById('contenedor-empleados');
    const tablaBody = document.querySelector('#tablaTareas tbody');
    const btnAgregarTarea = document.getElementById('btnAgregarTarea');
    const formProyecto = document.getElementById('formProyecto'); // Referencia al formulario

    function usuariosSeleccionados() {
        return Array.from(document.querySelectorAll('.check-empleado:checked')).map(c => ({
            id: c.value,
            nombre: c.dataset.nombre
        }));
    }

    // --- Lógica de carga de empleados ---
    selectDepto?.addEventListener('change', function() {
        const depto = departamentos.find(d => String(d.id) === String(this.value));
        contenedor.innerHTML = '';

        if (!depto || !depto.empleados || depto.empleados.length === 0) {
            contenedor.innerHTML = '<p class="text-center text-muted mt-3">No hay colaboradores.</p>';
            return;
        }

        depto.empleados.forEach(emp => {
            const nombre = `${emp.nombre} ${emp.apellido}`.trim();
            const idUser = emp.user_id;
            const html = `
                <div class="form-check mb-2 border-bottom pb-1">
                    <input class="form-check-input check-empleado" type="checkbox" name="designados[]" value="${idUser}" id="emp_${emp.id}" data-nombre="${nombre}">
                    <label class="form-check-label" for="emp_${emp.id}">
                        <strong>${nombre}</strong><br><small class="text-muted">${emp.cargo || 'Colaborador'}</small>
                    </label>
                </div>`;
            contenedor.insertAdjacentHTML('beforeend', html);
        });
    });

    // --- Lógica de tareas ---
    function filaTarea(index) {
        const users = usuariosSeleccionados();
        const options = users.length
            ? users.map(u => `<option value="${u.id}">${u.nombre}</option>`).join('')
            : '<option value="">Primero selecciona colaboradores</option>';

        return `
            <tr>
                <td><input name="tareas[${index}][titulo]" class="form-control form-control-sm" required></td>
                <td><select name="tareas[${index}][asignado_user_id]" class="form-select form-select-sm" required>${options}</select></td>
                <td><input type="date" name="tareas[${index}][fecha_inicio]" class="form-control form-control-sm" required></td>
                <td><input type="date" name="tareas[${index}][fecha_fin]" class="form-control form-control-sm" required></td>
                <td><input type="number" name="tareas[${index}][peso]" class="form-control form-control-sm" value="10" min="1" max="100"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"><i class="fas fa-trash"></i></button></td>
            </tr>`;
    }

    btnAgregarTarea?.addEventListener('click', function() {
        const index = tablaBody.querySelectorAll('tr').length;
        tablaBody.insertAdjacentHTML('beforeend', filaTarea(index));
    });

    tablaBody?.addEventListener('click', function(e) {
        if (e.target.closest('.btn-eliminar')) {
            e.target.closest('tr').remove();
        }
    });

    // --- MENSAJE DE CONFIRMACIÓN AL GUARDAR ---
    formProyecto?.addEventListener('submit', function(e) {
        e.preventDefault(); // Detenemos el envío automático

        const numTareas = tablaBody.querySelectorAll('tr').length;
        const numDesignados = usuariosSeleccionados().length;

        // Validación mínima
        if (numDesignados === 0) {
            Swal.fire('Atención', 'Debe seleccionar al menos un colaborador.', 'warning');
            return;
        }

        if (numTareas === 0) {
            Swal.fire('Atención', 'Debe agregar al menos una tarea al proyecto.', 'warning');
            return;
        }

        // Confirmación final
        Swal.fire({
            title: '¿Confirmar registro?',
            text: `Se creará el proyecto con ${numTareas} tareas asignadas.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, guardar todo',
            cancelButtonText: 'Revisar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, mostramos un loader y enviamos
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                formProyecto.submit();
            }
        });
    });
});
</script>