{{-- solicitudes/calculo_modal.blade.php --}}
<div class="p-4">
    {{-- ENCABEZADO DEL EMPLEADO --}}
    <div class="text-center mb-4">
        <div class="badge {{ strtolower($empleado->tipo_contrato) === 'permanente' ? 'bg-primary' : 'bg-info' }} text-uppercase mb-2 px-3 py-2">
           Contrato {{ $empleado->tipo_contrato }}
       </div>
        <h3 class="fw-bold text-dark mb-1">{{ strtoupper($empleado->nombre . ' ' . $empleado->apellido) }}</h3>
        <p class="text-muted mb-0">
            <i class="fas fa-id-card me-1"></i> {{ strtoupper($empleado->cargo) }}
        </p>
        <div class="mt-2 small">
            <span class="badge bg-light text-dark border">
                <i class="fas fa-calendar-alt me-1 text-primary"></i> 
                Ingreso: {{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}
            </span>
            <span class="badge bg-light text-dark border ms-2">
                <i class="fas fa-user-clock me-1 text-primary"></i> 
                Antigüedad: {{ $aniosCumplidos }} años completos
            </span>
        </div>
    </div>

    {{-- ALERTA INFORMATIVA DE LEY --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="border-radius: 12px;">
        <i class="fas fa-balance-scale fs-4 me-3 text-primary"></i>
        <div>
            <small class="d-block fw-bold text-primary text-uppercase" style="font-size: 0.7rem;">Base Legal: Artículo 346 del Código del Trabajo</small>
            <small class="text-dark">Cálculo progresivo: 1er año (10d), 2do año (12d), 3er año (15d), 4to año en adelante (20d).</small>
        </div>
    </div>

    {{-- TARJETAS DE RESUMEN --}}
    <div class="row g-3 mb-4">
        {{-- Derecho Acumulado --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 15px;">
                <div class="card-body text-center p-3">
                    <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Derecho Acumulado</div>
                    <div class="h2 fw-bold text-primary mb-0">{{ $totalDerechoHistorico }}</div>
                    <div class="text-muted small">Días totales ganado</div>
                </div>
            </div>
        </div>

        {{-- Días Gozados --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 15px;">
                <div class="card-body text-center p-3">
                    <div class="text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.7rem;">Días Gozados (-)</div>
                    <div class="h2 fw-bold text-danger mb-0">{{ $totalGozados }}</div>
                    <div class="text-muted small">Historial de aprobados</div>
                </div>
            </div>
        </div>

        {{-- Saldo Actual --}}
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm text-white" style="border-radius: 15px; background: linear-gradient(45deg, #2ecc71, #27ae60);">
                <div class="card-body text-center p-3">
                    <div class="small fw-bold text-uppercase mb-2" style="font-size: 0.7rem; opacity: 0.9;">Saldo Disponible</div>
                    <div class="h2 fw-bold mb-0 text-white">{{ $saldo }}</div>
                    <div class="small text-white-50">Días para goce real</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA DE HISTORIAL --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Historial de Vacaciones Aplicadas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 300px;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4">Año</th>
                            <th>Periodo de Disfrute</th>
                            <th class="text-center">Días Gastados</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $s)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($s->fecha_inicio)->format('Y') }}
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-success fw-bold">Desde:</span> {{ \Carbon\Carbon::parse($s->fecha_inicio)->format('d/m/Y') }}<br>
                                    <span class="text-danger fw-bold">Hasta:</span> {{ \Carbon\Carbon::parse($s->fecha_fin)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold px-3 py-2 border" style="font-size: 0.9rem;">
                                    {{ $s->dias }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success small fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Aprobado
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted italic">
                                <i class="fas fa-folder-open d-block mb-2 fs-3"></i>
                                No se registran vacaciones gozadas en el historial.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PIE DE PÁGINA --}}
    <div class="mt-4 d-flex justify-content-between align-items-center">
        <p class="text-muted x-small mb-0">
            * Este reporte es generado automáticamente y suma el derecho acumulado según la antigüedad del empleado.
        </p>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    .card-body .h2 { line-height: 1; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
</style>