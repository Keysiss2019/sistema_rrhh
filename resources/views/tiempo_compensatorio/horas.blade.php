{{-- ================= MODAL: REGISTRO DE HORAS EXTRA ================= --}}

<div class="modal fade" id="modalAcumular" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <form action="{{ route('horas_extras.store') }}" method="POST">
            @csrf
            <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
            <input type="hidden" name="codigo_formato" value="FT-GTH-002">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Formato FT-GTH-002 · Registro de Horas Extra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    {{-- LUGAR Y JEFE INMEDIATO --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Lugar</label>
                            <input type="text" name="lugar" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Solicitado a (Jefe Inmediato)</label>
                            <input type="text" name="solicitado_a" class="form-control" required>
                        </div>
                    </div>

                    {{-- SOLICITANTE Y CARGO --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Solicitado por</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $empleado->nombre }} {{ $empleado->apellido }}"
                                   readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Cargo</label>
                            <input type="text"
                                   name="cargo_solicitante"
                                   class="form-control bg-light"
                                   value="{{ $empleado->cargo }}"
                                   readonly
                                   required>
                        </div>
                    </div>

                    <hr>

                    {{-- ACTIVIDADES DINÁMICAS --}}
                    <div id="actividadesContainer">
                        <div class="actividadFila row mb-3">
                            <div class="col-md-3">
                                <label>Fecha</label>
                                <input type="date" name="fecha[]" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Hora Inicio</label>
                                <input type="time" name="hora_inicio[]" class="form-control horaInicio" required>
                            </div>
                            <div class="col-md-3">
                                <label>Hora Fin</label>
                                <input type="time" name="hora_fin[]" class="form-control horaFin" required>
                            </div>
                            <div class="col-md-3">
                                <label>Horas</label>
                                <input type="number" step="0.1" name="horas_trabajadas[]" class="form-control horas" readonly>
                            </div>
                            <div class="col-12 mt-2">
                                <label>Detalle Actividad</label>
                                <textarea name="actividad[]" class="form-control" required></textarea>
                            </div>
                             <div class="col-md-1">
            <button type="button" class="btn btn-danger btnEliminar w-100 d-none">
    <i class="fas fa-trash"></i>
</button>

        </div>
                        </div>
                       
                    </div>

                    <button type="button" id="agregarActividad" class="btn btn-secondary mb-3">
                        Agregar actividad
                    </button>

                    {{-- TOTAL DE HORAS --}}
                    <div class="mb-4">
                        <label class="fw-bold text-primary">
                            Total de horas trabajadas (según formato)
                        </label>
                        <input type="number"
                               step="0.1"
                               name="total_horas_trabajadas"
                               id="totalHoras"
                               class="form-control border-primary"
                               readonly
                               value="0">
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mb-3">
                        <label class="fw-bold text-muted">
                            Observación del Jefe (Opcional)
                        </label>
                        <textarea name="observaciones"
                                  rows="2"
                                  class="form-control"></textarea>
                    </div>

                    {{-- CONFIRMACIÓN DE RESPALDO --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input"
                               type="checkbox"
                               id="checkConfirmacion"
                               required>
                        <label class="form-check-label fw-bold text-danger" for="checkConfirmacion">
                            Confirmo que este registro corresponde al FT-GTH-002 y cuenta con respaldo físico.
                        </label>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Guardar Registro de Horas
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- SCRIPT PARA ACTIVIDADES DINÁMICAS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('actividadesContainer');
    const btnAgregar = document.getElementById('agregarActividad');
    const totalHorasInput = document.getElementById('totalHoras');

    function calcularHoras() {
        let total = 0;

        document.querySelectorAll('.actividadFila').forEach(fila => {
            const inicio = fila.querySelector('.horaInicio').value;
            const fin = fila.querySelector('.horaFin').value;
            let horas = 0;

            if (inicio && fin) {
                const [h1, m1] = inicio.split(':').map(Number);
                const [h2, m2] = fin.split(':').map(Number);
                horas = (h2 + m2 / 60) - (h1 + m1 / 60);
                if (horas < 0) horas = 0;
            }

            fila.querySelector('.horas').value = horas.toFixed(1);
            total += horas;
        });

        totalHorasInput.value = total.toFixed(1);
    }

    function actualizarBotonesEliminar() {
        const filas = document.querySelectorAll('.actividadFila');
        filas.forEach(fila => {
            const btnEliminar = fila.querySelector('.btnEliminar');
            if (filas.length > 1) {
                btnEliminar.classList.remove('d-none');
            } else {
                btnEliminar.classList.add('d-none');
            }
        });
    }

    // Agregar actividad
    btnAgregar.addEventListener('click', function () {
        const filaOriginal = container.querySelector('.actividadFila');
        const nuevaFila = filaOriginal.cloneNode(true);

        nuevaFila.querySelectorAll('input, textarea').forEach(el => el.value = '');
        container.appendChild(nuevaFila);

        actualizarBotonesEliminar();
    });

    // Eliminar actividad
    container.addEventListener('click', function (e) {
        if (e.target.closest('.btnEliminar')) {
            e.target.closest('.actividadFila').remove();
            calcularHoras();
            actualizarBotonesEliminar();
        }
    });

    container.addEventListener('input', calcularHoras);

    // Estado inicial
    actualizarBotonesEliminar();
});
</script>

