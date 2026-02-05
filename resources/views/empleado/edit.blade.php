<div class="modal fade" id="modalEditarEmpleado{{ $empleado->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            {{-- Encabezado --}}
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-user-pen me-2"></i>Editar Registro: {{ strtoupper($empleado->nombre) }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('empleado.update', $empleado->id) }}" method="POST" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        
                        {{-- 1. DATOS PERSONALES --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombres</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Apellidos</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $empleado->apellido) }}" class="form-control" required>
                        </div>

                        {{-- 2. CONTACTO --}}
                        <div class="col-md-6">
                          <label class="form-label fw-bold small text-uppercase">Correo Electrónico</label>
                          <div class="input-group">
                              <span class="input-group-text bg-light"><i class="fa-solid fa-envelope"></i></span>
                               {{-- Quitamos el 'required' para permitir que TI lo asigne después --}}
                               <input type="email" name="email" value="{{ old('email', $empleado->email) }}" 
                                class="form-control" placeholder="Pendiente de asignar por TI">
                         </div>
                          @if(!$empleado->email)
                             <small class="text-danger fw-bold" style="font-size: 0.7rem;">
                                 <i class="fa-solid fa-circle-info me-1"></i> SIN CORREO INSTITUCIONAL
                              </small>
                           @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CONTACTO</label>
                            <input type="text" name="contacto" value="{{ old('contacto', $empleado->contacto) }}" class="form-control">
                        </div>

                        {{-- 3. DATOS LABORALES --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">CARGO</label>
                            <input type="text" name="cargo" value="{{ old('cargo', $empleado->cargo) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small text-uppercase">Departamento</label>
                          <select name="departamento" class="form-select select-departamento-edit" required>
                             <option value="" disabled>-- Seleccione --</option>
                                @foreach($departamentos as $dep)
                                  <option value="{{ $dep->id }}" 
                                      data-jefe="{{ $dep->jefeEmpleado ? $dep->jefeEmpleado->nombre . ' ' . $dep->jefeEmpleado->apellido : 'Sin jefe asignado' }}"
                                      {{ $empleado->departamento_id == $dep->id ? 'selected' : '' }}>
                                      {{ $dep->nombre }}
                                 </option>
                               @endforeach
                          </select>
                       </div>

                       <div class="col-md-4">
                         <label class="form-label fw-bold small text-uppercase">Jefe Inmediato</label>
                          {{-- Agregamos una clase para identificarlo mediante JS --}}
                          <input type="text" name="jefe_inmediato" 
                            value="{{ $empleado->departamento?->jefeEmpleado ? $empleado->departamento->jefeEmpleado->nombre . ' ' . $empleado->departamento->jefeEmpleado->apellido : 'Sin jefe asignado' }}" 
                            class="form-control input-jefe-edit" readonly>
                       </div>

                       {{-- GESTIÓN DE CONTRATO --}}
                       <div class="col-12 mt-2">
                          
                          <label class="text-muted fw-bold" style="font-size: 0.75rem; display: block; margin-bottom: 4px;">
                             CAMBIAR TIPO DE CONTRATO
                          </label>
    
                          <div class="alert alert-warning d-none" id="alertaCambioContrato{{ $empleado->id }}">
                             <i class="fa-solid fa-triangle-exclamation me-2"></i>
                               <strong>Atención:</strong>
                                 Cambiar el tipo de contrato actualizará los días de vacaciones del empleado.
                          </div>

                          {{-- Cambiamos el name a "tipo_contrato" --}}
                         <select name="politica_id"  class="form-select select-politica-edit"
                             data-contrato-actual="{{ $empleado->tipo_contrato }}"
                              data-empleado="{{ $empleado->id }}" required>
                              @foreach($politicas as $politica)
                                 <option value="{{ $politica->id }}"
                                     data-contrato="{{ $politica->tipo_contrato }}"
                                      data-dias="{{ $politica->dias_anuales }}"
                                      {{ $empleado->tipo_contrato == $politica->tipo_contrato ? 'selected' : '' }}>
                                      {{ strtoupper($politica->tipo_contrato) }}
                                 </option>
                               @endforeach
                          </select>
                          <div class="mt-2">
                             <small class="text-muted">
                                  Días de vacaciones actuales:
                                   <strong>{{ $empleado->dias_vacaciones_anuales }} días</strong>
                              </small>
                          </div>

                      </div>

                        {{-- 5. FECHAS Y ESTADO --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success small">FECHA INGRESO</label>
                            <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger small">FECHA BAJA</label>
                            <input type="date" name="fecha_baja" value="{{ old('fecha_baja', $empleado->fecha_baja) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">ESTADO</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo" {{ $empleado->estado == 'activo' ? 'selected' : '' }}>🟢 ACTIVO</option>
                                <option value="inactivo" {{ $empleado->estado == 'inactivo' ? 'selected' : '' }}>🔴 INACTIVO</option>
                            </select>
                        </div>

                        {{-- 6. DOCUMENTO --}}
                        <div class="col-12 mt-3">
                            <hr class="text-muted">
                            <label class="form-label fw-bold small mb-2 text-uppercase">Expediente Digital</label>
                            
                            @if($empleado->documentos && $empleado->documentos->count() > 0)
                                @php 
                                    $doc = $empleado->documentos->first();
                                    $rutaLimpia = str_replace(['public/', 'storage/'], '', $doc->ruta_archivo);
                                @endphp
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $rutaLimpia) }}" target="_blank" class="text-danger small fw-bold text-decoration-none">
                                        <i class="fa-solid fa-file-pdf me-1"></i> VER ARCHIVO CARGADO
                                    </a>
                                </div>
                            @endif

                            <div class="input-group">
                                <label class="input-group-text bg-dark text-white"><i class="fa-solid fa-upload"></i></label>
                                <input type="file" name="documento" class="form-control" accept=".pdf,.jpg,.png">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('change', function(event) {
        // Verificamos si el elemento que cambió tiene nuestra clase de edición
        if (event.target && event.target.classList.contains('select-departamento-edit')) {
            const select = event.target;
            
            // Obtenemos el nombre del jefe desde el data-attribute
            const selectedOption = select.options[select.selectedIndex];
            const jefe = selectedOption.getAttribute('data-jefe') || 'Sin jefe asignado';
            
            // Buscamos el input de jefe que está en el mismo modal (contenedor padre .row)
            const modalBody = select.closest('.row');
            const inputJefe = modalBody.querySelector('.input-jefe-edit');
            
            if (inputJefe) {
                inputJefe.value = jefe;
            }
        }
    });
</script>

<script>
document.addEventListener('change', function (event) {

    if (!event.target.classList.contains('select-politica-edit')) return;

    const select = event.target;
    const contratoActual = select.dataset.contratoActual;
    const empleadoId = select.dataset.empleado;

    const selectedOption = select.options[select.selectedIndex];
    const contratoNuevo = selectedOption.dataset.contrato;

    const alerta = document.getElementById('alertaCambioContrato' + empleadoId);

    if (contratoNuevo !== contratoActual) {
        alerta.classList.remove('d-none');
    } else {
        alerta.classList.add('d-none');
    }
});
</script>