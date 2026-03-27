<div class="modal fade" id="modalAgregarFirma" tabindex="-1" aria-labelledby="modalAgregarFirmaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            {{-- Encabezado con estilo similar al de gestión --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalAgregarFirmaLabel">
                    <i class="fa-solid fa- signature-lock me-2"></i>Nuevo Paso de Aprobación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('configuracion.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small">
                        <i class="fa-solid fa-circle-info me-1"></i> 
                        Este nuevo paso se reflejará automáticamente en la tabla de gestión y en el formato de impresión.
                    </div>

                    {{-- Nombre Completo --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Nombre del Firmante / Área</label>
                        <input type="text" name="nombre_paso" class="form-control" 
                               placeholder="Ej: Encargado de Actividad" required>
                        <div class="form-text text-muted">Aparecerá en el cuadro de firma del documento.</div>
                    </div>

                    {{-- Nombre Corto --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Etiqueta Corta (Badge)</label>
                        <input type="text" name="nombre_corto" class="form-control" 
                               placeholder="Ej: Actividad" maxlength="15" required>
                        <div class="form-text text-muted">Es el texto que se ve dentro del círculo en la tabla.</div>
                    </div>

                    <div class="row">
                        {{-- Orden --}}
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Orden de Firma</label>
                            <input type="number" name="orden" class="form-control" 
                                   placeholder="Ej: 2" min="1" required>
                        </div>
                        
                        {{-- Icono --}}
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Icono (FontAwesome)</label>
                            <select name="icono" class="form-select">
                                <option value="fa-user-check">Jefe (Default)</option>
                                <option value="fa-person-running">Actividad</option>
                                <option value="fa-signature">Firma</option>
                                <option value="fa-building-user">Recursos Humanos</option>
                                <option value="fa-user-tie">Dirección</option>
                            </select>
                        </div>
                    </div>

                    {{-- Estado inicial --}}
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="activo" id="checkActivo" checked value="1">
                        <label class="form-check-label fw-bold" for="checkActivo">Activar inmediatamente</label>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4 shadow">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>