<!-- Modal para editar un paso específico -->
<div class="modal fade" id="modalEditarPaso-{{ $paso->id }}" tabindex="-1" aria-hidden="true">
    <!-- Contenedor del modal -->
    <div class="modal-dialog">
        <!-- Contenido principal del modal -->
        <div class="modal-content">
            
            <!-- Cabecera del modal con título y botón de cierre -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Paso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Formulario para actualizar el paso -->
            <form action="{{ route('configuracion.update', $paso->id) }}" method="POST">
                @csrf  <!-- Token CSRF para seguridad -->
                @method('PUT') <!-- Método HTTP PUT para actualización -->

                <!-- Cuerpo del modal con campos del formulario -->
                <div class="modal-body">
                    <!-- Campo: Nombre completo del paso -->
                    <label>Nombre del Paso</label>
                    <input type="text" name="nombre_paso" class="form-control mb-2" value="{{ $paso->nombre_paso }}">

                    <!-- Campo: Nombre corto del paso -->
                    <label>Nombre Corto</label>
                    <input type="text" name="nombre_corto" class="form-control mb-2" value="{{ $paso->nombre_corto }}">

                    <!-- Campo: Orden del paso en el flujo -->
                    <label>Orden</label>
                    <input type="number" name="orden" class="form-control" value="{{ $paso->orden }}">
                </div>

                <!-- Pie del modal con botón para enviar los cambios -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar la eliminación de un paso -->
<div class="modal fade" id="modalEliminarPaso-{{ $paso->id }}" tabindex="-1" aria-hidden="true">
    <!-- Contenedor pequeño y centrado -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <!-- Contenido principal del modal -->
        <div class="modal-content text-center p-3">
            <!-- Mensaje de confirmación -->
            <p>¿Eliminar el paso <b>{{ $paso->nombre_corto }}</b>?</p>

            <!-- Formulario para eliminar el paso -->
            <form action="{{ route('configuracion.destroy', $paso->id) }}" method="POST">
                @csrf <!-- Token CSRF -->
                @method('DELETE') <!-- Método HTTP DELETE -->

                <!-- Botón para confirmar eliminación -->
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>

                <!-- Botón para cancelar y cerrar el modal -->
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
            </form>
        </div>
    </div>
</div>