{{-- ================= MODAL: REGISTRO DE HORAS EXTRA ================= --}}
<div class="modal fade" id="modalAcumular" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        {{-- Formulario que envía los datos al controlador de Horas Extra --}}
        <form action="{{ route('horas_extras.store') }}" method="POST">
            @csrf {{-- Token de seguridad CSRF --}}

            {{-- ID del empleado al que se le registran las horas --}}
            <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">

            {{-- Código del formato oficial --}}
            <input type="hidden" name="codigo_formato" value="FT-GTH-002">

            <div class="modal-content">

                {{-- ================= HEADER ================= --}}
                <div class="modal-header bg-primary text-white">
                    {{-- Título del formulario --}}
                    <h5 class="modal-title">
                        Formato FT-GTH-002 · Registro de Horas Extra
                    </h5>

                    {{-- Botón para cerrar el modal --}}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- ================= BODY ================= --}}
                <div class="modal-body">

                    {{-- LUGAR Y JEFE INMEDIATO --}}
                    <div class="row mb-3">
                        {{-- Lugar donde se realizaron las horas extra --}}
                        <div class="col-md-6">
                            <label class="fw-bold">Lugar</label>
                            <input type="text" name="lugar" class="form-control" required>
                        </div>

                        {{-- Nombre del jefe inmediato --}}
                        <div class="col-md-6">
                            <label class="fw-bold">Solicitado a (Jefe Inmediato)</label>
                            <input type="text" name="solicitado_a" class="form-control" required>
                        </div>
                    </div>

                    {{-- SOLICITANTE Y CARGO --}}
                    <div class="row mb-3">
                        {{-- Nombre del empleado solicitante (solo lectura) --}}
                        <div class="col-md-6">
                            <label class="fw-bold">Solicitado por</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $empleado->nombre }} {{ $empleado->apellido }}"
                                   readonly>
                        </div>

                        {{-- Cargo del empleado (solo lectura) --}}
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

                    {{-- FECHAS DE LA ACTIVIDAD --}}
                    <div class="row mb-3">
                        {{-- Fecha de inicio de la actividad --}}
                        <div class="col-md-6">
                            <label class="fw-bold text-dark">Fecha Inicio de Actividad</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>

                        {{-- Fecha de fin de la actividad --}}
                        <div class="col-md-6">
                            <label class="fw-bold text-dark">Fecha Fin de Actividad</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                    </div>

                    {{-- TOTAL DE HORAS TRABAJADAS --}}
                    <div class="mb-4">
                        <label class="fw-bold text-primary">
                            Total de horas trabajadas (según formato)
                        </label>
                        {{-- El name debe coincidir con el controlador --}}
                        <input type="number"
                               step="0.1"
                               name="horas"
                               class="form-control border-primary"
                               required>
                    </div>

                    {{-- DETALLE DE ACTIVIDADES --}}
                    <div class="mb-3">
                        <label class="fw-bold">
                            Detalle de actividades realizadas
                        </label>
                        <textarea name="detalle_actividad"
                                  rows="4"
                                  class="form-control"
                                  placeholder="Describa las actividades realizadas según el formato físico"
                                  required></textarea>
                    </div>

                    {{-- OBSERVACIONES DEL JEFE --}}
                    <div class="mb-3">
                        <label class="fw-bold text-muted">
                            Observación del Jefe (Opcional)
                        </label>
                        <textarea name="observaciones"
                                  rows="2"
                                  class="form-control"></textarea>
                    </div>

                    {{-- CONFIRMACIÓN DE RESPALDO FÍSICO --}}
                    <div class="form-check mt-3">
                        <input class="form-check-input"
                               type="checkbox"
                               id="checkConfirmacion"
                               required>

                        <label class="form-check-label fw-bold text-danger"
                               for="checkConfirmacion">
                            Confirmo que este registro corresponde al FT-GTH-002 y cuenta con respaldo físico.
                        </label>
                    </div>

                </div>

                {{-- ================= FOOTER ================= --}}
                <div class="modal-footer">
                    {{-- Botón para guardar el registro --}}
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i> Guardar Registro de Horas
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

