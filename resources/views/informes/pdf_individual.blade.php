<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Individual de Desempeño</title>

    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #003366;
            padding-bottom: 10px;
        }

        .titulo {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
        }

        .info-reporte {
            margin-bottom: 20px;
            width: 100%;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tabla th {
            background-color: #003366;
            color: white;
            padding: 10px 8px;
            text-align: left;
        }

        .tabla td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 13px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .firmas {
            margin-top: 60px;
            width: 100%;
            text-align: center;
        }

        .espacio-firma {
            display: inline-block;
            width: 220px;
            border-top: 1px solid #000;
            margin: 0 40px;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    {{-- ENCABEZADO --}}
    <table style="width: 100%; border-bottom: 2px solid #003366; padding-bottom: 10px; margin-bottom: 20px;">
        <tr>

            {{-- Logo --}}
            <td style="width: 80px; vertical-align: middle;">

                @php
                    $path = public_path('images/IHCI.png');
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @endphp

                <img src="{{ $base64 }}" style="width: 70px; height: auto;">
            </td>

            {{-- Texto Central --}}
            <td style="text-align: center; vertical-align: middle;">

                <div class="titulo">
                    Instituto Hondureño de Cultura Interamericana
                </div>

                <div style="font-size: 14px; margin-top: 5px;">
                    Reporte Individual de Evaluación de Desempeño
                </div>

            </td>

            {{-- Espacio derecho --}}
            <td style="width: 80px;"></td>

        </tr>
    </table>


    {{-- INFORMACIÓN DEL REPORTE --}}
    <table class="info-reporte">

        <tr>
            <td>
                <strong>Colaborador:</strong>
                {{ $empleado->nombre }} {{ $empleado->apellido }}
            </td>

            <td align="right">
                <strong>Año:</strong>
                {{ $anio }}
            </td>
        </tr>

        <tr>
            <td>
                <strong>Departamento:</strong>
                {{ $empleado->departamento->nombre ?? 'N/A' }}
            </td>

            <td align="right">
                <strong>Fecha de Impresión:</strong>
                {{ date('d/m/Y') }}
            </td>
        </tr>

    </table>


    {{-- TABLA DE DATOS --}}
    <table class="tabla">

        <thead>
            <tr>
                <th width="55%">Actividad / Proyecto</th>
                <th width="25%">Fecha de Evaluación</th>
                <th width="20%" style="text-align: center;">Resultado</th>
            </tr>
        </thead>

        <tbody>

            @forelse($datos as $dato)

                <tr>

                    <td>
                        {{ $dato->actividad }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($dato->fecha)->format('d/m/Y') }}
                    </td>

                    <td align="center">
                        <strong style="color:#003366;">
                            {{ number_format($dato->resultado, 2) }}%
                        </strong>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="3" align="center">
                        No se registraron evaluaciones en este periodo.
                    </td>
                </tr>

            @endforelse

        </tbody>


        {{-- PROMEDIO --}}
       <tfoot>
         <tr class="total-row">
         <td colspan="2" align="right">
             RENDIMIENTO GLOBAL DEL COLABORADOR:
          </td>
          <td align="center" style="color:#003366;">
              {{-- Cambiamos $promedio por $promedio_global --}}
              {{ number_format($promedio_global ?? 0, 2) }}%
          </td>
         </tr>
      </tfoot>

    </table>


    {{-- NOTA --}}
    <div style="margin-top: 20px; font-style: italic; color: #555;">

        * Este reporte refleja el desempeño individual del colaborador,
        considerando las evaluaciones registradas durante el período seleccionado.

    </div>


    {{-- FIRMA --}}
    <div class="firmas">

        <br><br><br>

        <div class="espacio-firma">
            Sello Gerencia de Talento Humano
        </div>

    </div>


    {{-- FOOTER --}}
    <div class="footer">
        Documento de carácter institucional - Generado por Sistema RRHH IHCI
    </div>

</body>
</html>