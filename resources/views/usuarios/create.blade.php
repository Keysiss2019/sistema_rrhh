<!-- Offcanvas para registrar un nuevo usuario -->
<div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="offcanvasNuevoUsuario">

    <!-- Cabecera del offcanvas -->
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title fw-bold">
            <i class="fa-solid fa-user-plus me-2"></i> Registrar Usuario
        </h5>
        <!-- Botón para cerrar el offcanvas -->
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <!-- Cuerpo del offcanvas -->
    <div class="offcanvas-body">
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf <!-- Token de seguridad de Laravel -->

            <!-- Selección del empleado -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">EMPLEADO</label>
                <select name="empleado_id" class="form-select border-2 shadow-sm" required>
                    <option value="">Seleccione un empleado...</option>
                    @foreach($empleados as $emp)
                        <option value="{{ $emp->id }}">
                            {{ strtoupper($emp->nombre) }} {{ strtoupper($emp->apellido ?? '') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Campo de nombre de usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">NOMBRE DE USUARIO</label>
                <input type="text" name="usuario" class="form-control border-2" placeholder="Ej: jlopez" required>
            </div>

            <!-- Campo de contraseña temporal con botón para mostrar/ocultar -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">CONTRASEÑA TEMPORAL</label>
                <div class="input-group shadow-sm">
                    <input type="password" name="password" id="pass_new" class="form-control border-2" required>
                    <button class="btn btn-outline-secondary border-2" type="button" onclick="togglePass('pass_new')">
                        <i class="fa-solid fa-eye" id="icon_new"></i>
                    </button>
                </div>
            </div>

            <!-- Checkbox para forzar cambio de contraseña al ingresar -->
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="debe_cambiar_password" id="checkForce" value="1" checked>
                <label class="form-check-label fw-bold text-primary" for="checkForce">
                    Exigir cambio de contraseña al ingresar
                </label>
            </div>

            <!-- Selección del rol del usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">ROL DEL SISTEMA</label>
                <select name="role_id" class="form-select border-2" required>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selección del estado inicial del usuario -->
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary small">ESTADO INICIAL</label>
                <select name="estado" class="form-select border-2" required>
                    <option value="activo" selected>Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <!-- Botones de acción: Crear Usuario o Cancelar -->
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow fw-bold">
                    <i class="fa-solid fa-save me-2"></i> Crear Usuario
                </button>
                <button type="button" class="btn btn-secondary btn-lg fw-bold" data-bs-dismiss="offcanvas">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Script para mostrar/ocultar la contraseña temporal -->
<script>
    function togglePass(id) {
        const input = document.getElementById(id); // Input de contraseña
        const icon = document.getElementById('icon_new'); // Icono del ojo
        if (input.type === "password") {
            input.type = "text"; // Mostrar contraseña
            icon.classList.replace('fa-eye', 'fa-eye-slash'); // Cambiar icono
        } else {
            input.type = "password"; // Ocultar contraseña
            icon.classList.replace('fa-eye-slash', 'fa-eye'); // Cambiar icono
        }
    }
</script>


