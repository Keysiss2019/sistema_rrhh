{{-- MODAL PARA VALIDACIÓN DE TAREAS (RF-10) --}}
<div class="modal fade" id="modalTareasProyecto" tabindex="-1" aria-labelledby="modalTareasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalTareasLabel">
                    <i class="fas fa-check-circle me-2"></i>Tareas del Proyecto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenedor-tareas-ajax">
                    {{-- El contenido se genera dinámicamente con JavaScript --}}
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status"></div>
                        <p class="mt-2">Cargando tareas...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>