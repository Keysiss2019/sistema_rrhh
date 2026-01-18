<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="offcanvasNuevoEmpleado" style="width: 500px;">
    <div class="offcanvas-header bg-primary text-white py-4">
        <h5 class="offcanvas-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Registrar Empleado</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="{{ route('empleado.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                {{-- 1. IDENTIDAD --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small">NOMBRES</label>
                    <input type="text" name="nombre" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">APELLIDOS</label>
                    <input type="text" name="apellido" class="form-control shadow-sm" required>
                </div>
                
                <div class="col-md-7">
                    <label class="form-label fw-bold small">CORREO</label>
                    <input type="email" name="email" class="form-control shadow-sm" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-bold small">CONTACTO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-muted small"></i></span>
                        <input type="text" name="contacto" class="form-control shadow-sm" placeholder="Ej. 9988-7766">
                    </div>
                </div>

                {{-- 2. DATOS LABORALES --}}
                <div class="col-12">
                  <label class="form-label fw-bold small text-uppercase">Cargo / Puesto de Trabajo</label>
                  <div class="input-group">
                     <span class="input-group-text bg-light"><i class="fa-solid fa-briefcase text-muted"></i></span>
                      <input type="text" name="cargo" class="form-control shadow-sm" placeholder="Ej. Analista de Recursos Humanos" required>
                  </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase">Jefe Inmediato</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user-tie text-muted"></i></span>
                        <input type="text" name="jefe_inmediato" class="form-control shadow-sm" placeholder="Nombre del supervisor directo">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase">Departamento</label>
                    <input type="text" name="departamento" class="form-control shadow-sm" placeholder="Ej. Contabilidad / Operaciones" required>
                </div>

                {{-- CORRECCIÓN: Quitamos el $empleado->tipo_contrato porque aquí no existe --}}
                <div class="col-12">
                    <label class="form-label fw-bold small text-primary">TIPO DE CONTRATO (POLÍTICAS)</label>
                    <select name="politica_id" class="form-select" required>
                        <option value="" selected disabled>-- Seleccione Contrato --</option>
                        @foreach($politicas as $politica)
                           <option value="{{ $politica->id }}">
                              {{ strtoupper($politica->tipo_contrato) }}
                          </option>
                       @endforeach
                  </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">FECHA NACIMIENTO</label>
                    <input type="date" name="fecha_nacimiento" class="form-control shadow-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">ESTADO</label>
                    <select name="estado" class="form-select shadow-sm">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-success">FECHA INGRESO</label>
                    <input type="date" name="fecha_ingreso" class="form-control border-success shadow-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-danger">FECHA BAJA</label>
                    <input type="date" name="fecha_baja" class="form-control border-danger shadow-sm">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase">Adjuntar Expediente / Contrato</label>
                    <input type="file" name="documentos[]" class="form-control shadow-sm" multiple>
                    <input type="hidden" name="tipos_documento[]" value="Contrato Inicial">
                </div>

                {{-- Botones de acción directos --}}
                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="offcanvas">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>