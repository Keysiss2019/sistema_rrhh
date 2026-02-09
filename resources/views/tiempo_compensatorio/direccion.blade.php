@extends('layouts.app')

@section('content')
<style>
    /* Estilos para simular la hoja de papel FT-GTH-002 */
    .document-paper {
        background-color: #fff;
        font-family: Arial, Helvetica, sans-serif !important;
        color: #000 !important;
        border: 2px solid #000 !important;
        padding: 40px;
        margin: 0 auto;
        line-height: 1.2;
    }
    .document-paper table {
        width: 100%;
        border-collapse: collapse !important;
        margin-bottom: 0;
    }
    .document-paper table, .document-paper td, .document-paper th {
        border: 1px solid #000 !important;
        padding: 5px;
    }
    .bg-formato { 
        background-color: #e9ecef !important; 
        font-size: 11px; 
        font-weight: bold; 
        text-transform: uppercase;
    }
    .text-value {
        font-weight: bold;
        font-size: 13px;
    }
</style>

<div class="container py-4">
    <div class="card border-0 shadow-sm p-4">
        <h3 class="fw-bold text-dark mb-4">
            <i class="fa-solid fa-file-signature text-primary me-2"></i>
            Revisión de Horas Extras (Dirección)
        </h3>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Empleado</th>
                        <th>Período</th>
                        <th class="text-center">Total Horas</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendientes as $h)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $h->empleado->nombre }} {{ $h->empleado->apellido }}</div>
                                <small class="text-muted">{{ $h->cargo_solicitante }}</small>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($h->fecha_inicio)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($h->fecha_fin)->format('d/m/Y') }}
                            </td>
                            <td class="text-center fw-bold text-primary">
                                {{ number_format($h->horas_trabajadas, 2) }} h
                            </td>
                            <td><span class="badge bg-warning text-dark">{{ ucfirst($h->estado) }}</span></td>
                            <td class="text-center">
                                <button class="btn btn-dark shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modal{{ $h->id }}">
                                    VER FORMATO
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5">No hay solicitudes pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODALES FUERA DE LA TABLA --}}
{{-- MODALES --}}
@foreach($pendientes as $h)
<div class="modal fade" id="modal{{ $h->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title small">Formato FT-GTH-002: {{ $h->empleado->nombre }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="{{ route('direccion.horas_extras.decidir', $h->id) }}">
                @csrf
                <div class="modal-body p-4 bg-light">
                    {{-- HOJA DE PAPEL --}}
                    <div class="document-paper mx-auto shadow-sm">
                        
                        {{-- ENCABEZADO IDÉNTICO AL FORMATO FT-GTH-002 --}}
                        <table class="table-formato text-center">
                            <tr>
                                <td rowspan="3" style="width: 20%; border: 1px solid #000;">
                                    <img src="{{ asset('img/logo_ihci.png') }}" style="width: 80px;">
                                </td>
                                <td class="bg-formato" style="width: 25%;">FORMATO</td>
                                <td class="bg-formato" style="width: 25%;">CÓDIGO</td>
                                <td style="width: 30%;">FT-GTH-002</td>
                            </tr>
                            <tr>
                                <td rowspan="2" class="fw-bold" style="font-size: 11px;">SOLICITUD DE AUTORIZACIÓN DE TIEMPO COMPENSADO</td>
                                <td class="bg-formato">VERSIÓN</td>
                                <td>1.1</td>
                            </tr>
                            <tr>
                                <td class="bg-formato small">VIGENTE DESDE</td>
                                <td>28/03/2023</td>
                            </tr>
                        </table>

                        {{-- DATOS GENERALES --}}
                        <div class="border-container">
                            <div class="row-formato">
                                <div class="label-formato" style="width: 20%;">Lugar y fecha:</div>
                                <div class="value-formato">{{ $h->lugar }}, {{ now()->format('d/m/Y') }}</div>
                            </div>
                            <div class="row-formato">
                                <div class="label-formato" style="width: 20%;">Solicitado a:</div>
                                <div class="value-formato fw-bold">{{ $h->solicitado_a }}</div>
                            </div>
                            <div class="row-formato no-bottom">
                                <div class="label-formato" style="width: 20%;">Solicitado por:</div>
                                <div class="value-formato border-end" style="width: 45%;">{{ $h->empleado->nombre }} {{ $h->empleado->apellido }}</div>
                                <div class="label-formato border-start" style="width: 10%;">Cargo:</div>
                                <div class="value-formato">{{ $h->cargo_solicitante }}</div>
                            </div>
                        </div>

                        <div class="p-3">
                            Por este medio solicito me autorice: <span class="px-3 border-bottom border-dark fw-bold">{{ number_format($h->horas_trabajadas, 2) }}</span> horas a cuenta de tiempo compensatorio.
                        </div>

                        {{-- TABLA DE ACTIVIDADES (CORREGIDA SEGÚN TU OBSERVACIÓN) --}}
                        <div class="px-2 pb-2">
                            <p class="fw-bold mb-1" style="font-size: 10px;">Detalle de actividades realizadas a cuenta de tiempo compensatorios:</p>
                            <table class="table-formato text-center small">
                                <thead class="bg-formato">
                                    <tr>
                                        <th>Fecha</th>
                                        <th style="width: 40%;">Descripción de actividad realizada</th>
                                        <th>Hora de inicio</th>
                                        <th>Hora de finalización</th>
                                        <th>Total de horas realizada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="height: 50px;">
                                        <td>{{ \Carbon\Carbon::parse($h->fecha_inicio)->format('d/m/Y') }}</td>
                                        <td class="text-start ps-2">{{ $h->detalle_actividad }}</td>
                                        {{-- MOSTRANDO HORA INICIO Y HORA FIN SEGÚN EL FORMATO --}}
                                        <td>{{ $h->hora_inicio ?? '0:00' }}</td> 
                                        <td>{{ $h->hora_fin ?? '0:00' }}</td>
                                        <td class="fw-bold">{{ number_format($h->horas_trabajadas, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- FIRMAS --}}
                        <div class="row text-center mt-5 mb-3" style="font-size: 9px;">
                            <div class="col-4"><div class="mx-3 border-top border-dark pt-1">Vo. Bo. Del Jefe Inmediato</div></div>
                            <div class="col-4"><div class="mx-3 border-top border-dark pt-1">Vo.Bo. Área a cargo de la actividad</div></div>
                            <div class="col-4"><div class="mx-3 border-top border-dark pt-1">Vo.Bo. G.T.H.</div></div>
                        </div>
                    </div>

                    {{-- SECCIÓN DE APROBACIÓN PARA DIRECCIÓN --}}
                    <div class="mt-4 p-3 border border-primary bg-white rounded shadow-sm">
                        <h6 class="text-primary fw-bold small mb-2"><i class="fa-solid fa-check-to-slot me-2"></i>Aprobación de Dirección Ejecutiva</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="small fw-bold">Horas a pagar en efectivo:</label>
                                <input type="number" step="0.01" name="horas_pagadas" class="form-control form-control-sm border-primary">
                            </div>
                            <div class="col-md-8">
                                <label class="small fw-bold">Observaciones:</label>
                                <textarea name="observaciones" class="form-control form-control-sm border-primary" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 justify-content-center">
                    <button type="submit" name="accion" value="rechazado" class="btn btn-outline-danger px-4">RECHAZAR</button>
                    <button type="submit" name="accion" value="aprobado" class="btn btn-success px-5 fw-bold shadow">APROBAR DIRECCIÓN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection