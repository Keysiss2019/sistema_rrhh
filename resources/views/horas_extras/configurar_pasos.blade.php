<div class="modal fade" id="modalEditarPaso-{{ $paso->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Paso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('configuracion.update', $paso->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <label>Nombre del Paso</label>
                    <input type="text" name="nombre_paso" class="form-control mb-2" value="{{ $paso->nombre_paso }}">
                    <label>Nombre Corto</label>
                    <input type="text" name="nombre_corto" class="form-control mb-2" value="{{ $paso->nombre_corto }}">
                    <label>Orden</label>
                    <input type="number" name="orden" class="form-control" value="{{ $paso->orden }}">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarPaso-{{ $paso->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <p>¿Eliminar el paso <b>{{ $paso->nombre_corto }}</b>?</p>
            <form action="{{ route('configuracion.destroy', $paso->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">No</button>
            </form>
        </div>
    </div>
</div>