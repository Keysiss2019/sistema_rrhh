<!-- Offcanvas para editar un usuario existente -->
<div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="offcanvasEditarUsuario">

    <!-- Cabecera del offcanvas -->
    <div class="offcanvas-header bg-warning text-dark">
        <h5 class="offcanvas-title fw-bold">
            <i class="fa-solid fa-user-pen me-2"></i> Editar Usuario
        </h5>
        <!-- Botón para cerrar el offcanvas -->
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <!-- Cuerpo del offcanvas -->
    <div class="offcanvas-body">
        <form id="formEditarUsuario" method="POST" action="">
            @csrf <!-- Token de seguridad de Laravel -->
            @method('PUT') <!-- Método PUT para actualizar el recurso -->

            <!-- Campo del empleado (solo lectura, no editable) -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">EMPLEADO (No editable)</label>
                <input type="text" id="edit_empleado" class="form-control border-2 bg-light" readonly>
            </div>

            <!-- Campo para editar nombre de usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">NOMBRE DE USUARIO</label>
                <input type="text" name="usuario" id="edit_usuario" class="form-control border-2 shadow-sm" required>
            </div>

            <!-- Campo para asignar nueva contraseña opcional -->
           <div class="mb-4 p-3 border rounded bg-light">
              <div class="form-check form-switch">
                 <input class="form-check-input" type="checkbox" name="reset_password" id="resetPasswordCheck" value="1">
                 <label class="form-check-label fw-bold text-dark" for="resetPasswordCheck">
                     <i class="fa-solid fa-key me-1 text-warning"></i> Generar nueva contraseña temporal
                  </label>
           </div>
           <div class="form-text small">
               Si se activa, se enviará una clave genérica al correo del usuario y se le obligará a cambiarla al entrar.
          </div>
</div>

            <!-- Selección del rol del usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">ROL DEL SISTEMA</label>
                <select name="role_id" id="edit_role_id" class="form-select border-2 shadow-sm" required>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selección del estado del usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">ESTADO</label>
                <select name="estado" id="edit_estado" class="form-select border-2 shadow-sm" required>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <!-- Botones de acción: Actualizar Usuario o Cancelar -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-warning btn-lg shadow fw-bold">
                    <i class="fa-solid fa-sync me-2"></i> Actualizar Usuario
                </button>
                <button type="button" class="btn btn-secondary btn-lg fw-bold" data-bs-dismiss="offcanvas">Cancelar</button>
            </div>
        </form>
    </div>
</div>
