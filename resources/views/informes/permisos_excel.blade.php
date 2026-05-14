<table>
    {{-- Espacios para el logo y encabezado --}}
    <tr><td colspan="6" style="font-weight: bold; font-size: 16pt; text-align: center;">INSTITUTO HONDUREÑO DE CULTURA INTERAMERICANA</td></tr>
    <tr><td colspan="6" style="text-align: center;">Reporte de Permisos</td></tr>
    <tr><td colspan="6"></td></tr>

    <tr>
        <td style="font-weight: bold;">Colaborador:</td>
        <td colspan="2">{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
        <td style="font-weight: bold;">Año Fiscal:</td>
        <td>{{ $anio }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Departamento:</td>
        <td colspan="2">{{ $empleado->departamento->nombre ?? 'N/A' }}</td>
        <td style="font-weight: bold;">Periodo:</td>
        <td>{{ $mes }}</td>
    </tr>
    <tr><td></td></tr>

    <thead>
        <tr>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Tipo de Permiso</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Fecha Inicio</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Fecha Fin</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Días</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Horas</th>
            <th style="background-color: #003366; color: #ffffff; font-weight: bold;">Observaciones</th>
        </tr>
    </thead>
  <tbody>
        @foreach($solicitudes as $s)
        <tr>
            <td>{{ $s->tipo }}</td>
            <td>{{ $s->fecha_inicio }}</td>
            <td>{{ $s->fecha_fin }}</td>
            <td style="text-align: center;">{{ $s->dias }}</td>
            <td style="text-align: center;">{{ $s->horas }}</td>
            <td>{{ $s->motivo }}</td>
        </tr>
        @endforeach
    </tbody>
<tfoot style="background-color: #ffffff;">
    {{-- FILA DE TOTALES: Asegúrate de que estas variables tengan valor --}}
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold; background-color: #ffffff;">TOTALES:</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid #000; background-color: #ffffff;">
            {{ $total_dias ?? 0 }}
        </td>
        <td style="text-align: center; font-weight: bold; border: 1px solid #000; background-color: #ffffff;">
            {{ $total_horas ?? 0 }}
        </td>
        <td style="background-color:  #ffffff;"></td>
    </tr>

    {{-- ESPACIO PARA LA FIRMA (La imagen caerá aquí) --}}
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>
    <tr><td colspan="6" style="background-color: #ffffff;">&nbsp;</td></tr>

    {{-- LÍNEA Y TEXTO DEBAJO DE LA FIRMA --}}
    <tr>
        <td style="background-color: #ffffff;"></td>
        <td colspan="3" style="border-top: 2px solid #000000; text-align: center; font-weight: bold; background-color: #ffffff;">
            GESTIÓN DE TALENTO HUMANO
        </td>
        <td style="background-color: #ffffff;"></td>
        <td style="background-color: #ffffff;"></td>
    </tr>
    <tr>
        <td style="background-color: #ffffff;"></td>
        <td colspan="3" style="text-align: center; background-color: #ffffff;">IHCI</td>
        <td style="background-color: #ffffff;"></td>
        <td style="background-color: #ffffff;"></td>
    </tr>
</tfoot>
</table>