{{-- ================= MODAL: REGISTRO DE HORAS EXTRA ================= --}}
<div class="modal fade" id="modalAcumular" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('horas_extras.store') }}" method="POST">
            @csrf
            {{-- Campos ocultos técnicos --}}
            <input type="hidden" name="codigo_formato" value="FT-GTH-002">
            <input type="hidden" name="estado" value="pendiente"> {{-- Estado inicial según tu ENUM --}}

            <div class="modal-content">
                <div class="offcanvas-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Formato FT-GTH-002 · Registro de Horas Extra
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- SELECCIÓN DE COLABORADOR POR DEPARTAMENTO --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-daek"><i class="fas fa-building me-1"></i> 1. Departamento (Colaborador)</label>
                            <select id="modal_depto_select" name="departamento" class="form-select border-primary" required>
                                <option value="">-- Seleccione el departamento --</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->nombre }}" data-id="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-dark"><i class="fas fa-user me-1"></i> 2. Colaborador (ID se guarda en BD)</label>
                            <select id="modal_empleado_select" name="empleado_id" class="form-select border-primary" required>
                                <option value="">-- Seleccione un departamento primero --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Lugar (Sede)</label>
                            <input type="text" name="lugar" class="form-control" placeholder="Ej. Tegucigalpa" required>
                        </div>
                       <div class="col-md-6">
                          <label class="fw-bold text-muted">Solicitado a (Autorizante):</label>
                            {{-- Tomamos el nombre y apellido del empleado logueado --}}
                           <input type="text" 
                             name="nombre" 
                             class="form-control bg-light" 
                             value="{{ auth()->user()->empleado->nombre }} {{ auth()->user()->empleado->apellido }}" 
                             readonly 
                            required>
                            <small class="text-muted italic">Registrado por el jefe en sesión.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- TABLA DINÁMICA DE ACTIVIDADES --}}
                    <div id="wrapperActividades">
                        <div class="fila-actividad row mb-3 align-items-end bg-light p-3 rounded border mx-0">
                            <div class="col-md-3">
                                <label class="small fw-bold">Fecha</label>
                                <input type="date" name="fecha[]" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">H. Inicio</label>
                                <input type="time" name="hora_inicio[]" class="form-control h-inicio" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">H. Fin</label>
                                <input type="time" name="hora_fin[]" class="form-control h-fin" required>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">Subtotal</label>
                                <input type="text" step="0.1" name="horas_trabajadas[]" class="form-control h-subtotal bg-white" readonly>
                            </div>
                            <div class="col-md-3 text-end">
                                <button type="button" class="btn btn-outline-danger btn-borrar-fila d-none">
                                    <i class="fas fa-times me-1"></i> Eliminar
                                </button>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="small fw-bold">Descripción de la actividad</label>
                                <textarea name="actividad[]" class="form-control" rows="1" placeholder="¿Qué labor realizó?" required></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="btnNuevaFila" class="btn btn-outline-secondary btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i> Añadir Actividad
                    </button>

                    <div class="row mt-2">
                        <div class="col-md-2 ms-auto">
                            <div class="card bg-primary text-white p-2 shadow-sm">
                                <label class="fw-bold">TOTAL HORAS ACUMULADAS</label>
                                <input type="text" step="0.1" name="horas_acumuladas" id="inputTotalAcumulado" class="form-control form-control-lg fw-bold text-center" readonly value="0">
                            </div>
                        </div>
                    </div>

                  {{-- En tu modal, cambia el bloque de la firma por este --}}
                 <div id="vistaPreviaFirmaSuperior" class="text-center d-none">
                     <img id="imgFirmaSuperior" src="" style="height: 70px;">
                      <p>___________________________</p>
                      <p>Vo. Bo. Jefe Inmediato</p>
                  </div>
                    <div class="mt-4">
                        <label class="fw-bold">Observaciones del Jefe Inmediato</label>
                        <textarea name="observaciones_jefe" class="form-control" rows="2" placeholder="Notas adicionales sobre el desempeño o la solicitud..."></textarea>
                    </div>

                    <div class="alert alert-warning mt-4 py-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmarFisico" required>
                            <label class="form-check-label fw-bold small text-dark" for="confirmarFisico">
                                Certifico que los datos coinciden con el formato físico FT-GTH-002.
                            </label>
                        </div>
                    </div>
                </div>

               <div class="modal-footer bg-light d-flex align-items-center justify-content-between">
  
                   {{-- AREA CENTRAL: Botón Firmar y Vista Previa --}}
                    <div class="d-flex align-items-center border p-2 bg-white rounded shadow-sm">
                      <button type="button" id="btnCargarFirma" class="btn btn-outline-primary me-3">
                         <i class="fas fa-pen-fancy me-2"></i>Firmar Solicitud
                       </button>
        
                      <div id="contenedorFirmaJefe" class="d-none text-center">
                         <small class="text-muted d-block">Firma del Jefe:</small>
                          <img id="imgFirmaJefe" src="" style="max-height: 60px; border-bottom: 2px solid #333;">
                          {{-- Input oculto para enviarla al servidor si es necesario, o solo validación --}}
                          <input type="hidden" name="tiene_firma" id="inputTieneFirma" value="0">
                       </div>
                   </div>

                    {{-- Botón 3: Guardar y Firmar (Inicia deshabilitado) --}}
                    <button type="submit" name="accion" value="guardar_y_firmar" id="btnGuardarFirmado"class="btn btn-primary px-4 shadow">
                      <i class="fa-solid fa-floppy-disk me-1"></i>Guardar y Finalizar
                   </button>
                 </div>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptoSelect = document.getElementById('modal_depto_select');
    const empSelect = document.getElementById('modal_empleado_select');
    const dataDeptos = @json($departamentos);

    // 1. FILTRADO DE EMPLEADOS
    deptoSelect.addEventListener('change', function() {
        const selectedDepto = this.value;
        const deptoObj = dataDeptos.find(d => d.nombre === selectedDepto);
        empSelect.innerHTML = '<option value="">-- Seleccione Colaborador --</option>';
        if (deptoObj && deptoObj.empleados) {
            deptoObj.empleados.forEach(e => {
                const opt = document.createElement('option');
                opt.value = e.id;
                opt.textContent = `${e.nombre} ${e.apellido}`.toUpperCase();
                empSelect.appendChild(opt);
            });
        }
    });

    // 2. CÁLCULO DINÁMICO DE HORAS
    const contenedor = document.getElementById('wrapperActividades');
    const inputGlobalTotal = document.getElementById('inputTotalAcumulado');

    function refrescarCalculos() {
        let granTotalMinutos = 0;
        document.querySelectorAll('.fila-actividad').forEach(fila => {
            const hI = fila.querySelector('.h-inicio').value;
            const hF = fila.querySelector('.h-fin').value;
            if (hI && hF) {
                const [h1, m1] = hI.split(':').map(Number);
                const [h2, m2] = hF.split(':').map(Number);
                let diferencia = ((h2 * 60) + m2) - ((h1 * 60) + m1);
                if (diferencia < 0) diferencia += 1440;
                
                const horasReloj = Math.floor(diferencia / 60);
                const minutosReloj = diferencia % 60;
                fila.querySelector('.h-subtotal').value = `${horasReloj}:${minutosReloj.toString().padStart(2, '0')}`;
                granTotalMinutos += diferencia;
            }
        });
        const totalH = Math.floor(granTotalMinutos / 60);
        const totalM = granTotalMinutos % 60;
        inputGlobalTotal.value = `${totalH}:${totalM.toString().padStart(2, '0')}`;
    }

    contenedor.addEventListener('input', refrescarCalculos);

    // 3. AGREGAR / ELIMINAR FILAS
    const btnAgregar = document.getElementById('btnNuevaFila');
    btnAgregar.addEventListener('click', () => {
        const filasActivas = document.querySelectorAll('.fila-actividad');
        if (filasActivas.length < 5) {
            const clon = filasActivas[0].cloneNode(true);
            clon.querySelectorAll('input, textarea').forEach(el => el.value = '');
            clon.querySelector('.btn-borrar-fila').classList.remove('d-none');
            contenedor.appendChild(clon);
            if (document.querySelectorAll('.fila-actividad').length === 5) btnAgregar.style.display = 'none';
        }
    });

    contenedor.addEventListener('click', (e) => {
        if (e.target.closest('.btn-borrar-fila')) {
            e.target.closest('.fila-actividad').remove();
            refrescarCalculos();
            if (document.querySelectorAll('.fila-actividad').length < 5) btnAgregar.style.display = 'inline-block';
        }
    });

    // ==========================================
    // 🟢 LÓGICA DE FIRMA (CORREGIDA)
    // ==========================================
    const btnCargarFirma = document.getElementById('btnCargarFirma');
    const contenedorFirma = document.getElementById('contenedorFirmaJefe');
    const imgFirma = document.getElementById('imgFirmaJefe');
    const btnGuardarFirmado = document.getElementById('btnGuardarFirmado');
    const inputTieneFirma = document.getElementById('inputTieneFirma');

    if (btnCargarFirma) {
        btnCargarFirma.addEventListener('click', function() {
            btnCargarFirma.disabled = true;
            btnCargarFirma.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';

            fetch("{{ route('firma.get') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar vista previa de la firma
                        imgFirma.src = data.firma;
                        contenedorFirma.classList.remove('d-none');
                        
                        // Habilitar el botón final y marcar como firmado
                        btnGuardarFirmado.disabled = false;
                        inputTieneFirma.value = "1";
                        
                        // Ocultar botón de búsqueda
                        btnCargarFirma.classList.add('d-none');
                    } else {
                        alert('Error: ' + data.message);
                        btnCargarFirma.disabled = false;
                        btnCargarFirma.innerHTML = '<i class="fas fa-pen-fancy me-2"></i>Firmar Solicitud';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo conectar con el servidor de firmas.');
                    btnCargarFirma.disabled = false;
                });
        });
    }
});
</script>