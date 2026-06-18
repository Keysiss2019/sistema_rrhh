<table>
    <thead>
        {{-- Añade 4 filas vacías al principio para que el logo (A1) no tape el título --}}
        <tr></tr>
        <tr></tr>
      
        {{-- Encabezado Institucional (Cubre las 6 columnas) --}}
        <tr>
            <td style="width: 100px;"></td> {{-- Espacio para el logo --}}

            <th colspan="6" style="font-size: 16pt; font-weight: bold; text-align: left; color: #003366;">
                INSTITUTO HONDUREÑO DE CULTURA INTERAMERICANA
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 12pt; text-align: left; font-weight: bold;">
                Reporte de Desempeño Individual del Colaborador
            </th>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- Información Personal --}}
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #f2f2f2;">Colaborador:</td>
            <td colspan="4" style="text-transform: uppercase;">{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #f2f2f2;">Departamento:</td>
            <td colspan="4">{{ $empleado->departamento->nombre }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #f2f2f2;">Período:</td>
            <td colspan="4">{{ $periodo_texto }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold; background-color: #f2f2f2;">Año Fiscal:</td>
            <td colspan="4">{{ $anio }}</td>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- Encabezados de la Tabla (6 columnas exactas) --}}
        <tr style="background-color: #003366; color: #ffffff;">
            <th style="border: 1px solid #000000;">Actividad / Proyecto</th>
            <th style="border: 1px solid #000000;">Formulario</th>
            <th style="border: 1px solid #000000;">Tipo</th>
            <th style="border: 1px solid #000000;">Departamento</th>
            <th style="border: 1px solid #000000;">Fecha</th>
            <th style="border: 1px solid #000000;">Resultado</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach($datos as $dato)
           <tr>
                <td style="border: 1px solid #000000; padding: 10px;">{{ $dato->actividad }}</td>
                <td style="border: 1px solid #000000; padding: 10px;">{{ $dato->nombre_formulario ?? 'N/A' }}</td>
                <td style="border: 1px solid #000000; padding: 10px;">{{ $dato->tipo }}</td>
                <td sstyle="border: 1px solid #000000; padding: 10px;">{{ $dato->depto_evaluador }}</td>
                <td style="border: 1px solid #000000; padding: 10px;">{{ \Carbon\Carbon::parse($dato->fecha)->format('d/m/Y') }}</td>
                <td style="border: 1px solid #000000; padding: 10px;">{{ number_format($dato->resultado, 2) }}%</td>
           </tr>
        @endforeach
    </tbody>

   <tfoot>
      <tr>
         <td colspan="5" style="text-align: right; font-weight: bold; border: 1px solid #000000;">PROMEDIO DE RENDIMIENTO:</td>
          <td style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #f2f2f2;">
             {{ number_format($promedio_individual, 2) }}%
         </td>
      </tr>
    
      {{-- Mantenemos el espacio mediante margen en el estilo, no con filas vacías --}}
      <tr><td colspan="6" style="height: 120px;"></td></tr>

      <tr>
         <td></td>
         <td colspan="3" style="border-top: 2px solid #000000; text-align: center; font-weight: bold;">
             GESTIÓN DE TALENTO HUMANO
         </td>
         <td colspan="2"></td>
      </tr>
       <tr>
         <td></td>
         <td colspan="3" style="text-align: center;">GTH</td>
         <td colspan="2"></td>
      </tr>
   </tfoot>
</table>