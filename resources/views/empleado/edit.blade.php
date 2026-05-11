<!-- ========================================================= -->
<!-- MODAL PARA EDITAR INFORMACIÓN DEL EMPLEADO -->
<!-- ========================================================= -->

<div class="modal fade"
     id="modalEditarEmpleado{{ $empleado->id }}"
     tabindex="-1"
     aria-hidden="true">

    <!-- Tamaño grande y centrado -->
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <!-- Contenedor principal -->
        <div class="modal-content border-0 shadow-lg">

            {{-- ========================================================= --}}
            {{-- ENCABEZADO DEL MODAL --}}
            {{-- ========================================================= --}}

            <div class="modal-header text-white py-3">

                <!-- Título dinámico -->
                <h5 class="modal-title fw-bold">

                    <i class="fa-solid fa-user-pen me-2"></i>

                    Editar Registro:
                    {{ strtoupper($empleado->nombre) }}
                </h5>

                <!-- Botón cerrar -->
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>
            </div>


            <!-- ===================================================== -->
            <!-- FORMULARIO DE ACTUALIZACIÓN -->
            <!-- ===================================================== -->

            <form action="{{ route('empleado.update', $empleado->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                <!-- Token de seguridad -->
                @csrf

                <!-- Método PUT para actualización -->
                @method('PUT')

                <div class="modal-body p-4">

                    <div class="row g-3">

                        {{-- ========================================================= --}}
                        {{-- 0. IDENTIFICACIÓN --}}
                        {{-- ========================================================= --}}

                        <!-- Código de empleado -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold small text-black">
                                CÓDIGO DE EMPLEADO
                            </label>

                            <div class="input-group">

                                <!-- Icono -->
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-id-badge"></i>
                                </span>

                                <!-- Campo readonly -->
                                <input type="text"
                                       name="codigo_empleado"
                                       value="{{ old('codigo_empleado', $empleado->codigo_empleado) }}"
                                       class="form-control bg-light"
                                       readonly
                                       title="El código no se puede editar">
                            </div>
                        </div>


                        <!-- DNI -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold small text-black">
                                DNI / IDENTIDAD
                            </label>

                            <div class="input-group">

                                <!-- Icono -->
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-fingerprint"></i>
                                </span>

                                <!-- Input -->
                                <input type="text"
                                       name="dni"
                                       value="{{ old('dni', $empleado->dni) }}"
                                       class="form-control"
                                       placeholder="0000-0000-00000"
                                       required>
                            </div>
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 1. DATOS PERSONALES --}}
                        {{-- ========================================================= --}}

                        <!-- Nombres -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold">
                                Nombres
                            </label>

                            <input type="text"
                                   name="nombre"
                                   value="{{ old('nombre', $empleado->nombre) }}"
                                   class="form-control"
                                   required>
                        </div>


                        <!-- Apellidos -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold">
                                Apellidos
                            </label>

                            <input type="text"
                                   name="apellido"
                                   value="{{ old('apellido', $empleado->apellido) }}"
                                   class="form-control"
                                   required>
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 2. CONTACTO --}}
                        {{-- ========================================================= --}}

                        <!-- Correo -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold small text-uppercase">
                                Correo Electrónico
                            </label>

                            <div class="input-group">

                                <!-- Icono -->
                                <span class="input-group-text bg-light">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>

                                <!-- Input correo -->
                                <!-- No es obligatorio -->
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $empleado->email) }}"
                                       class="form-control"
                                       placeholder="Pendiente de asignar por TI">
                            </div>

                            <!-- Mensaje si no tiene correo -->
                            @if(!$empleado->email)

                                <small class="text-danger fw-bold"
                                       style="font-size: 0.7rem;">

                                    <i class="fa-solid fa-circle-info me-1"></i>

                                    SIN CORREO INSTITUCIONAL
                                </small>

                            @endif
                        </div>


                        <!-- Contacto -->
                        <div class="col-md-6">

                            <label class="form-label fw-bold small">
                                CONTACTO
                            </label>

                            <input type="text"
                                   name="contacto"
                                   value="{{ old('contacto', $empleado->contacto) }}"
                                   class="form-control">
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 3. DATOS LABORALES --}}
                        {{-- ========================================================= --}}

                        <!-- Cargo -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold small">
                                CARGO
                            </label>

                            <input type="text"
                                   name="cargo"
                                   value="{{ old('cargo', $empleado->cargo) }}"
                                   class="form-control">
                        </div>


                        <!-- Departamento -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold small text-uppercase">
                                Departamento
                            </label>

                            <select name="departamento"
                                    class="form-select select-departamento-edit"
                                    required>

                                <option value="" disabled>
                                    -- Seleccione --
                                </option>

                                <!-- Listado dinámico -->
                                @foreach($departamentos as $dep)

                                    <option value="{{ $dep->id }}"

                                        <!-- Nombre del jefe -->
                                        data-jefe="{{ $dep->jefeEmpleado ? $dep->jefeEmpleado->nombre . ' ' . $dep->jefeEmpleado->apellido : 'Sin jefe asignado' }}"

                                        <!-- Departamento actual -->
                                        {{ $empleado->departamento_id == $dep->id ? 'selected' : '' }}>

                                        {{ $dep->nombre }}
                                    </option>

                                @endforeach
                            </select>
                        </div>


                        <!-- Jefe inmediato -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold small text-uppercase">
                                Jefe Inmediato
                            </label>

                            <input type="text"
                                   name="jefe_inmediato"

                                   <!-- Nombre dinámico -->
                                   value="{{ $empleado->departamento?->jefeEmpleado ? $empleado->departamento->jefeEmpleado->nombre . ' ' . $empleado->departamento->jefeEmpleado->apellido : 'Sin jefe asignado' }}"

                                   class="form-control input-jefe-edit"
                                   readonly>
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 4. GESTIÓN DE CONTRATO --}}
                        {{-- ========================================================= --}}

                        <div class="col-12 mt-2">

                            <!-- Etiqueta -->
                            <label class="text-muted fw-bold"
                                   style="font-size: 0.75rem; display: block; margin-bottom: 4px;">

                                CAMBIAR TIPO DE CONTRATO
                            </label>


                            <!-- Alerta dinámica -->
                            <div class="alert alert-warning d-none"
                                 id="alertaCambioContrato{{ $empleado->id }}">

                                <i class="fa-solid fa-triangle-exclamation me-2"></i>

                                <strong>Atención:</strong>

                                Cambiar el tipo de contrato actualizará
                                los días de vacaciones del empleado.
                            </div>


                            <!-- Selector políticas -->
                            <select name="politica_id"
                                    class="form-select select-politica-edit"

                                    <!-- Contrato actual -->
                                    data-contrato-actual="{{ $empleado->tipo_contrato }}"

                                    <!-- ID empleado -->
                                    data-empleado="{{ $empleado->id }}"
                                    required>

                                @foreach($politicas as $politica)

                                    <option value="{{ $politica->id }}"

                                        <!-- Tipo contrato -->
                                        data-contrato="{{ $politica->tipo_contrato }}"

                                        <!-- Días vacaciones -->
                                        data-dias="{{ $politica->dias_anuales }}"

                                        <!-- Contrato seleccionado -->
                                        {{ $empleado->tipo_contrato == $politica->tipo_contrato ? 'selected' : '' }}>

                                        {{ strtoupper($politica->tipo_contrato) }}
                                    </option>

                                @endforeach
                            </select>


                            <!-- Información actual -->
                            <div class="mt-2">

                                <small class="text-muted">

                                    Días de vacaciones actuales:

                                    <strong>
                                        {{ $empleado->dias_vacaciones_anuales }} días
                                    </strong>
                                </small>
                            </div>
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 5. FECHAS Y ESTADO --}}
                        {{-- ========================================================= --}}

                        <!-- Fecha ingreso -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold text-success small">
                                FECHA INGRESO
                            </label>

                            <input type="date"
                                   name="fecha_ingreso"
                                   value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}"
                                   class="form-control"
                                   required>
                        </div>


                        <!-- Fecha baja -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold text-danger small">
                                FECHA BAJA
                            </label>

                            <input type="date"
                                   name="fecha_baja"
                                   value="{{ old('fecha_baja', $empleado->fecha_baja) }}"
                                   class="form-control">
                        </div>


                        <!-- Estado -->
                        <div class="col-md-4">

                            <label class="form-label fw-bold small">
                                ESTADO
                            </label>

                            <select name="estado"
                                    class="form-select"
                                    required>

                                <option value="activo"
                                    {{ $empleado->estado == 'activo' ? 'selected' : '' }}>

                                    🟢 ACTIVO
                                </option>

                                <option value="inactivo"
                                    {{ $empleado->estado == 'inactivo' ? 'selected' : '' }}>

                                    🔴 INACTIVO
                                </option>
                            </select>
                        </div>


                        {{-- ========================================================= --}}
                        {{-- 6. EXPEDIENTE DIGITAL --}}
                        {{-- ========================================================= --}}

                        <div class="col-12 mt-3">

                            <hr class="text-muted">

                            <label class="form-label fw-bold small mb-2 text-uppercase">
                                Expediente Digital
                            </label>


                            <!-- Validamos documentos -->
                            @if($empleado->documentos &&
                                $empleado->documentos->count() > 0)

                                @php

                                    // Primer documento encontrado
                                    $doc = $empleado->documentos->first();

                                    // Limpiamos rutas
                                    $rutaLimpia = str_replace(
                                        ['public/', 'storage/'],
                                        '',
                                        $doc->ruta_archivo
                                    );

                                @endphp

                                <!-- Link documento -->
                                <div class="mb-2">

                                    <a href="{{ asset('storage/' . $rutaLimpia) }}"
                                       target="_blank"
                                       class="text-danger small fw-bold text-decoration-none">

                                        <i class="fa-solid fa-file-pdf me-1"></i>

                                        VER ARCHIVO CARGADO
                                    </a>
                                </div>

                            @endif


                            <!-- Input archivo -->
                            <div class="input-group">

                                <label class="input-group-text bg-dark text-white">

                                    <i class="fa-solid fa-upload"></i>
                                </label>

                                <input type="file"
                                       name="documento"
                                       class="form-control"
                                       accept=".pdf,.jpg,.png">
                            </div>
                        </div>

                    </div>
                </div>


                {{-- ========================================================= --}}
                {{-- FOOTER --}}
                {{-- ========================================================= --}}

                <div class="modal-footer bg-light border-top">

                    <!-- Botón cancelar -->
                    <button type="button"
                            class="btn btn-secondary btn-lg fw-bold"
                            data-bs-dismiss="modal">

                        Cancelar
                    </button>

                    <!-- Botón actualizar -->
                    <button type="submit"
                            class="btn text-white rounded-pill px-4"
                            style="background-color: #054084;">

                        <i class="fa-solid fa-floppy-disk me-2"></i>

                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- SCRIPT PARA ACTUALIZAR JEFE INMEDIATO -->
<!-- ========================================================= -->

<script>
document.addEventListener('change', function(event) {

    // Validamos si el select cambiado es de departamento
    if (event.target &&
        event.target.classList.contains('select-departamento-edit')) {

        // Select actual
        const select = event.target;

        // Opción seleccionada
        const selectedOption = select.options[select.selectedIndex];

        // Obtenemos nombre del jefe
        const jefe =
            selectedOption.getAttribute('data-jefe')
            || 'Sin jefe asignado';

        // Buscamos el contenedor del modal
        const modalBody = select.closest('.row');

        // Input jefe inmediato
        const inputJefe =
            modalBody.querySelector('.input-jefe-edit');

        // Si existe el input
        if (inputJefe) {

            // Actualizamos valor
            inputJefe.value = jefe;
        }
    }
});
</script>


<!-- ========================================================= -->
<!-- SCRIPT ALERTA CAMBIO DE CONTRATO -->
<!-- ========================================================= -->

<script>
document.addEventListener('change', function (event) {

    // Validamos que sea selector de política
    if (!event.target.classList.contains('select-politica-edit'))
        return;

    // Selector actual
    const select = event.target;

    // Contrato actual
    const contratoActual =
        select.dataset.contratoActual;

    // ID empleado
    const empleadoId =
        select.dataset.empleado;

    // Opción seleccionada
    const selectedOption =
        select.options[select.selectedIndex];

    // Nuevo contrato
    const contratoNuevo =
        selectedOption.dataset.contrato;

    // Alerta específica
    const alerta =
        document.getElementById(
            'alertaCambioContrato' + empleadoId
        );

    // Si el contrato cambió
    if (contratoNuevo !== contratoActual) {

        // Mostrar alerta
        alerta.classList.remove('d-none');

    } else {

        // Ocultar alerta
        alerta.classList.add('d-none');
    }
});
</script>