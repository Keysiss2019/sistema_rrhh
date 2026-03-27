<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0.8cm; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.3; color: #333; }
        .tabla-bordeada { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .tabla-bordeada th, .tabla-bordeada td { border: 1px solid black; padding: 5px; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .bg-light { background-color: #eeeeee; }
        
        /* Estilo para los bloques de datos generales */
        .info-block { width: 100%; background-color: #eeeeee; border: 1px solid #ccc; padding: 8px; margin-bottom: 5px; }
        
        /* Contenedor de firmas */
        .firma-table { width: 100%; margin-top: 30px; table-layout: fixed; }
        .firma-cell { text-align: center; padding: 10px; vertical-align: bottom; }
        .linea-firma { border-top: 1px solid #333; margin-top: 5px; font-size: 9px; padding-top: 5px; }
        .img-firma { max-height: 60px; max-width: 150px; }
    </style>
</head>
<body>

    {{-- 1. CABECERA --}}
    <table class="tabla-bordeada">
        <tr>
            <td rowspan="4" style="width:20%; text-align:center;">
                <img src="{{ public_path('images/ihci_logo.jpg') }}" style="max-height:50px;">
            </td>
            <td class="text-center fw-bold">FORMATO</td>
            <td class="text-center fw-bold" style="font-size:9px;">CÓDIGO</td>
            <td class="text-center" style="font-size:10px;">FT-GTH-002</td>
        </tr>
        <tr>
            <td rowspan="3" class="text-center fw-bold" style="font-size:12px;">SOLICITUD DE AUTORIZACIÓN DE TIEMPO COMPENSADO</td>
            <td class="text-center fw-bold" style="font-size:9px;">VERSIÓN</td>
            <td class="text-center" style="font-size:10px;">1.1</td>
        </tr>
        <tr>
            <td class="text-center fw-bold" style="font-size:9px;">VIGENTE DESDE</td>
            <td class="text-center" style="font-size:10px;">28/3/2023</td>
        </tr>
        <tr>
            <td colspan="2" class="text-center fw-bold" style="font-size:9px;">PÁGINA 1 DE 1</td>
        </tr>
    </table>

    {{-- 2. DATOS GENERALES --}}
    <div class="info-block">
        <strong>LUGAR Y FECHA:</strong> {{ strtoupper($solicitud->lugar) }}, {{ $solicitud->created_at->format('d/m/Y') }}
    </div>

    <div class="info-block">
        <strong>SOLICITADO A:</strong> 
        @php
            $nombreJefe = 'JEFE DE DEPARTAMENTO';
            if ($solicitud->empleado && $solicitud->empleado->departamento) {
                $jefe = \App\Models\Empleado::find($solicitud->empleado->departamento->jefe_empleado_id);
                if($jefe) $nombreJefe = $jefe->nombre . ' ' . $jefe->apellido;
            }
        @endphp
        {{ strtoupper($nombreJefe) }}
    </div>

    <table style="width: 100%; border-spacing: 0; margin-bottom: 15px;">
        <tr>
            <td style="width: 65%; padding-right: 5px;">
                <div class="info-block">
                    <strong>SOLICITADO POR:</strong> {{ strtoupper($solicitud->empleado->nombre . ' ' . $solicitud->empleado->apellido) }}
                </div>
            </td>
            <td style="width: 35%;">
                <div class="info-block">
                    <strong>CARGO:</strong> {{ strtoupper($solicitud->cargo_solicitante ?? $solicitud->empleado->cargo) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. CÁLCULO DE HORAS --}}
    @php 
        $totalMinutosReloj = 0; 
        foreach($solicitud->detalles as $det) {
            for($i = 1; $i <= 10; $i++) {
                $campoAct = "actividad{$i}";
                if(!empty($det->$campoAct)) {
                    $h_ini = \Carbon\Carbon::parse($det->{"hora_inicio".$i});
                    $h_fin = \Carbon\Carbon::parse($det->{"hora_fin".$i});
                    if ($h_fin->lt($h_ini)) { $h_fin->addDay(); }
                    $totalMinutosReloj += $h_ini->diffInMinutes($h_fin);
                }
            }
        }
        $horasTotal = floor($totalMinutosReloj / 60);
        $minutosTotal = $totalMinutosReloj % 60;
        $totalFinalExito = $horasTotal . "." . str_pad($minutosTotal, 2, "0", STR_PAD_LEFT);
    @endphp

    <div style="margin: 15px 0;">
        Por este medio solicito me autorice: 
        <span style="background-color: #dddddd; padding: 5px 15px; border: 1px solid #ccc; font-weight: bold;">
            {{ $totalFinalExito }}
        </span> 
        <strong>horas</strong> a cuenta de tiempo compensatorio.
    </div>

    <p><strong>Detalle de actividades realizadas:</strong></p>

    {{-- 4. TABLA DE ACTIVIDADES --}}
    <table class="tabla-bordeada text-center">
        <thead class="bg-light">
            <tr style="font-size:10px;">
                <th>FECHA</th>
                <th style="width: 45%;">ACTIVIDAD REALIZADA</th>
                <th>INICIO</th>
                <th>FIN</th>
                <th>TOTAL HRS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solicitud->detalles as $det)
                @for($i = 1; $i <= 10; $i++)
                    @php $act = "actividad{$i}"; @endphp
                    @if(!empty($det->$act))
                        @php
                            $h_ini = \Carbon\Carbon::parse($det->{"hora_inicio".$i});
                            $h_fin = \Carbon\Carbon::parse($det->{"hora_fin".$i});
                            $diffMin = $h_ini->diffInMinutes($h_fin);
                            $f_hrs = floor($diffMin / 60) . "." . str_pad($diffMin % 60, 2, "0", STR_PAD_LEFT);
                        @endphp
                        <tr style="font-size:10px;">
                            <td>{{ \Carbon\Carbon::parse($det->{"fecha".$i})->format('d/m/Y') }}</td>
                            <td class="text-start">{{ strtoupper($det->$act) }}</td>
                            <td>{{ $h_ini->format('H:i') }}</td>
                            <td>{{ $h_fin->format('H:i') }}</td>
                            <td class="fw-bold">{{ $f_hrs }}</td>
                        </tr>
                    @endif
                @endfor
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-light fw-bold">
                <td colspan="4" class="text-end">TOTAL HORAS:</td>
                <td>{{ $totalFinalExito }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- 5. SECCIÓN DE FIRMAS DINÁMICAS --}}
    <table class="firma-table">
        <tr>
            @foreach($pasosConfigurados->where('activo', 1)->values() as $idx => $p)
                @php
                    // Usamos el helper app() para acceder al controlador y obtener el ID del jefe
                    $idJefePaso = app(\App\Http\Controllers\HoraExtraController::class)->obtenerJefeId($p, $solicitud, $idx);
                    $imgFirma = null;

                    // IMPORTANTE: Un paso se considera firmado si paso_actual es mayor que el índice
                    // O si el estado es 'aprobado' (paso final)
                    if (($solicitud->paso_actual > $idx || $solicitud->estado == 'aprobado') && $idJefePaso && $idJefePaso != -1) {
                        $f = DB::table('firmas')->where('empleado_id', $idJefePaso)->where('activo', 1)->first();
                        if ($f) {
                            $imgFirma = is_resource($f->imagen_path) ? stream_get_contents($f->imagen_path) : $f->imagen_path;
                        }
                    }
                @endphp
                <td class="firma-cell">
                    <div style="height: 70px;">
                        @if($imgFirma)
                            <img src="data:image/png;base64,{{ base64_encode($imgFirma) }}" class="img-firma">
                        @else
                            <span style="color: #ccc; font-size: 8px;">PENDIENTE</span>
                        @endif
                    </div>
                    <div class="linea-firma">
                        <strong>{{ strtoupper($p->nombre_paso) }}</strong>
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    {{-- 6. NOTA DE PAGO (Si existe) --}}
    @if($solicitud->horas_pagadas > 0)
        <div style="margin-top: 20px; border: 1px dashed #000; padding: 10px; font-size: 12px; background-color: #f9f9f9;">
            <i class="fas fa-info-circle"></i> <strong>NOTA DE DIRECCIÓN:</strong> 
            Se autoriza el pago de <strong>{{ $solicitud->horas_pagadas }}</strong> horas de las {{ $solicitud->horas_trabajadas }} solicitadas.
        </div>
    @endif

</body>
</html>