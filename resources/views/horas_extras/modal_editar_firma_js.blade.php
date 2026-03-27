<!-- Modal de edición de un paso/firma -->
<div class="modal fade" id="modalEditarFirma" tabindex="-1" aria-hidden="true">
    <!-- Contenedor centrado del modal -->
    <div class="modal-dialog modal-dialog-centered">
        <!-- Contenido principal del modal -->
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Cabecera del modal -->
            <div class="modal-header bg-warning text-dark">
                <!-- Título del modal con icono -->
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Editar Paso
                </h5>
                <!-- Botón de cierre del modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Formulario para editar el paso -->
            <form id="formEditarFirma" method="POST">
                @csrf  <!-- Token CSRF para seguridad -->
                @method('PUT') <!-- Método HTTP PUT para actualizar -->

                <!-- Cuerpo del modal -->
                <div class="modal-body">

                    <!-- Campo para el nombre completo del paso -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Paso</label>
                        <input type="text" name="nombre_paso" id="edit_nombre_paso" class="form-control" required>
                    </div>

                    <!-- Fila con dos columnas: nombre corto y orden -->
                    <div class="row">
                        <!-- Columna izquierda: nombre corto -->
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Nombre Corto</label>
                            <input type="text" name="nombre_corto" id="edit_nombre_corto" class="form-control" required>
                        </div>

                        <!-- Columna derecha: orden del paso -->
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Orden</label>
                            <input type="number" name="orden" id="edit_orden_paso" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Pie del modal con botones -->
                <div class="modal-footer">
                    <!-- Botón para cerrar/cancelar sin guardar -->
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <!-- Botón para enviar el formulario y guardar cambios -->
                    <button type="submit" class="btn btn-warning fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>