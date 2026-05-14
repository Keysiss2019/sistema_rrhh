<table>
    <!-- ENCABEZADO -->
    <tr>
        <td></td> {{-- Espacio para el logo que inserta el Controller --}}
        <th colspan="4" style="font-size: 16pt; font-weight: bold; text-align: left; color: #003366;">
            INSTITUTO HONDUREÑO DE CULTURA INTERAMERICANA
        </th>
    </tr>

    <tr>
        <td></td>
        <th colspan="4" style="text-align: center;">Reporte de Tiempo Compensatorio - {{ $anio }}</th>
    </tr>
    <tr><td colspan="5"></td></tr>

    <!-- INFO COLABORADOR -->
    <tr>
        <td><b>Colaborador:</b></td>
        <td colspan="2">{{ strtoupper($empleado->nombre . ' ' . $empleado->apellido) }}</td>
        <td><b>Departamento:</b></td>
        <td>{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><b>Código:</b></td>
        <td colspan="2">{{ $empleado->codigo_empleado }}</td>
        <td><b>Generado:</b></td>
        <td>{{ date('d/m/Y') }}</td>
    </tr>
    <tr><td colspan="5"></td></tr>

    <!-- TABLA DE DATOS (Fila 10) -->
    <thead>
        <tr>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Fecha</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Descripció de actividad</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Acum. (+)</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Cons. (-)</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold; border: 1px solid #000000;">Pag. ($)</th>
        </tr>
    </thead>
    <tbody>
        @php $tAcum = 0; $tCons = 0; $tPag = 0; @endphp
        @foreach($todosLosRegistros as $item)
            @php
                $esMov = isset($item->horas_acumuladas);
                $hA = $esMov ? ($item->horas_acumuladas ?? 0) : 0;
                $hP = $esMov ? ($item->horas_pagadas ?? 0) : 0;
                $hC = !$esMov ? (($item->horas > 0) ? $item->horas : (($item->dias ?? 0) * 8)) : 0;
                $tAcum += $hA; $tCons += $hC; $tPag += $hP;
                $fecha = $esMov ? $item->fecha : $item->fecha_inicio;
                $desc = $esMov ? ($item->descripcion ?? 'ACTIVIDAD') : ($item->tipo ?? 'SOLICITUD');
            @endphp
            <tr>
                {{-- Importante: No ponemos estilos de fondo aquí --}}
                <td style="background-color: #ffffff; border: 1px solid #cccccc;">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                <td style="background-color: #ffffff; border: 1px solid #cccccc;">{{ strtoupper($desc) }}</td>
                <td style="background-color: #ffffff; border: 1px solid #cccccc;">{{ $hA > 0 ? $hA : '' }}</td>
                <td style="background-color: #ffffff; border: 1px solid #cccccc;">{{ $hC > 0 ? $hC : '' }}</td>
                <td style="background-color: #ffffff; border: 1px solid #cccccc;">{{ $hP > 0 ? $hP : '' }}</td>
            </tr>
        @endforeach
    </tbody>

    <!-- TOTALES -->
    <tr><td colspan="5"></td></tr>
    <tr>
        <td colspan="3"></td>
        <td><b>TOTAL ACUMULADO:</b></td>
        <td style="text-align: right;">{{ number_format($tAcum, 2) }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td><b>TOTAL CONSUMIDO:</b></td>
        <td style="color: #ff0000; text-align: right;">{{ number_format($tCons, 2) }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td><b>TOTAL PAGADO:</b></td>
        <td style="color: #0000ff; text-align: right;">{{ number_format($tPag, 2) }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td style="background-color: #f0f0f0;"><b>SALDO DISPONIBLE:</b></td>
        <td style="background-color: #f0f0f0; font-weight: bold; text-align: right;">{{ number_format($tAcum - $tCons - $tPag, 2) }}</td>
    </tr>

    <!-- FIRMA -->
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr>
    <tr><td colspan="5"></td></tr> {{-- Espacio para que la imagen de la firma no tape el texto --}}
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold;">__________________________________</td>
    </tr>
    <tr>
        <td colspan="5" style="text-align: center;">Gestión de Talento Humano</td>
    </tr>
</table>