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
                            <label class="form-label fw-bold small">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" value="{{ old('email', $empleado->email) }}" class="form-control" required>
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
                            <label class="form-label fw-bold small">DEPARTAMENTO</label>
                            <input type="text" name="departamento" value="{{ old('departamento', $empleado->departamento) }}" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">JEFE INMEDIATO</label>
                            <input type="text" name="jefe_inmediato" value="{{ old('jefe_inmediato', $empleado->jefe_inmediato) }}" class="form-control">
                        </div>

                       {{-- GESTIÓN DE CONTRATO --}}
                       <div class="col-12 mt-2">
                          <div class="mb-0">
                             <span class="fw-bold text-primary" style="font-size: 0.9rem;">
                                 CONTRATO ACTUAL: {{ $empleado->tipo_contrato ?: 'SIN REGISTRO' }}
                               </span>
                          </div>

                          <label class="text-muted fw-bold" style="font-size: 0.75rem; display: block; margin-bottom: 4px;">
                             CAMBIAR TIPO DE CONTRATO
                          </label>
    
                          {{-- Cambiamos el name a "tipo_contrato" --}}
                         <select name="politica_id" class="form-select" required>
    @foreach($politicas as $politica)
        <option value="{{ $politica->id }}" 
            {{ $empleado->tipo_contrato == $politica->tipo_contrato ? 'selected' : '' }}>
            {{ strtoupper($politica->tipo_contrato) }}
        </option>
    @endforeach
</select>
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