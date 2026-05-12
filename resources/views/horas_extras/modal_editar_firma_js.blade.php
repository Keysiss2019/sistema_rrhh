<div class="modal fade" id="modalEditarFirma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Paso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarFirma" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Paso</label>
                        <input type="text" name="nombre_paso" id="edit_nombre_paso" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Nombre Corto</label>
                            <input type="text" name="nombre_corto" id="edit_nombre_corto" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Orden</label>
                            <input type="number" name="orden" id="edit_orden_paso" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>