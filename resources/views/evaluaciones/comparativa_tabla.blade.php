@php
    $nombreReal = $empleado->nombre . ' ' . $empleado->apellido;
    $promedioHistorico = $promedioGeneral ?? 0;
    $path = public_path('imges/IHCI.png'); 
    $base64 = '';
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp


{{-- 1. ENCABEZADO FORMAL IHCI --}}
<div id="encabezado-pdf" class="d-none w-100 mb-4" style="background: white; border: 1px solid #dee2e6;">
    {{-- BARRA AZUL --}}
   <div style="background-color: #4595e6; color: white; padding: 12px;">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                {{-- Logo a la izquierda --}}
               <td style="width: 80px; padding-left: 10px; vertical-align: middle;">
                    {{-- Usando tu ruta asset --}}
                    <img src="{{ asset('images/IHCI.png') }}" alt="IHCI" style="width: 70px; height: auto; display: block;">
                </td>

                {{-- Títulos centrados --}}
                <td style="text-align: center; vertical-align: middle;">
                    <h5 style="margin: 0; font-size: 0.95rem; font-weight: bold; text-transform: uppercase; font-family: sans-serif;">
                        Instituto Hondureño de Cultura Interamericana
                    </h5>
                    {{-- Nombre del formulario sin bordes --}}
                    <div style="margin-top: 4px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: normal;">
                        @if(count($historial) > 0)
                            {{ $historial[0]->nombre_formulario ?? 'Evaluación de Desempeño' }}
                        @else
                            Evaluación de Desempeño
                        @endif
                    </div>
                </td>
                
                {{-- Espacio para equilibrio --}}
                <td style="width: 70px;"></td>
            </tr>
        </table>
    </div>
    
    {{-- LÍNEA ROJA --}}
    <div style="height: 5px; background-color: #d9534f; width: 100%;"></div>

    {{-- TABLA DE DATOS (Dos columnas reales) --}}
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed; background-color: #f8f9fa;">
        <tr>
            {{-- COLUMNA IZQUIERDA: DATOS DEL EVALUADO --}}
            <td style="width: 50%; padding: 15px; border-right: 1px solid #dee2e6; vertical-align: top;">
                <div style="color: #666; font-size: 0.7rem; font-weight: bold; margin-bottom: 10px; text-transform: uppercase;">Datos del Evaluado</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70px; font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Nombre:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0;" id="pdf_empleado_nombre">{{ $nombreReal }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Cargo:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0;" id="pdf_empleado_cargo">{{ $empleado->cargo ?? 'Técnico Informático' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Depto:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0;" id="pdf_empleado_depto">{{ $empleado->departamento->nombre ?? 'Tecnología' }}</td>
                    </tr>
                </table>
            </td>

            {{-- COLUMNA DERECHA: INFORMACIÓN DEL REPORTE --}}
            <td style="width: 50%; padding: 15px; vertical-align: top;">
                <div style="color: #666; font-size: 0.7rem; font-weight: bold; margin-bottom: 10px; text-transform: uppercase;">Información del Reporte</div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 80px; font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Proyecto:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0;" id="pdf_proyecto_nombre">SISTEMA DE RRHH</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Fecha:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0;">{{ date('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 0.85rem; padding: 3px 0;">Promedio:</td>
                        <td style="font-size: 0.85rem; padding: 3px 0; font-weight: bold; color: #003366;">{{ number_format($promedioHistorico, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- 2. VISTA PARA LA WEB (LO QUE EL USUARIO VE) --}}
<div class="card card-soft p-4 mb-3 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">{{ $nombreReal }}</h4>
            <span class="badge bg-primary">Puntaje Consolidado: {{ number_format($promedioHistorico,2) }}</span>
        </div>
        <div class="text-end">
            <i class="fas fa-chart-line fa-2x text-muted opacity-25"></i>
        </div>
    </div>
</div>

@if(empty($historial) || count($historial) == 0)
    <div class="alert alert-warning border-0 shadow-sm">
        <i class="fas fa-exclamation-triangle me-2"></i> No se encontraron evaluaciones finalizadas para los criterios seleccionados.
    </div>
@else

    {{-- DATOS PARA CHART.JS --}}
    <div id="datos-grafica"
         data-labels='@json($labelsGrafica)'
         data-valores='@json($valoresGrafica)'></div>

    {{-- CONTENEDOR DE GRÁFICA --}}
    <div class="card card-soft mb-4 p-3 shadow-sm" style="background: white;">
        <div style="height: 250px; position: relative;">
            <canvas id="graficaEvolucion"></canvas>
        </div>
    </div>

    {{-- TABLA DE DETALLES --}}
 <div class="table-responsive card card-soft shadow-sm border-0">
    <table class="table table-hover align-middle mb-0" style="table-layout: fixed; width: 100%; border-collapse: collapse;">
        <thead class="bg-light">
            <tr>
                {{-- Definimos anchos fijos en % para que sumen 100% y no se corten --}}
                <th class="ps-4" style="width: 30%; font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 8px;">Fecha</th>
                <th style="width: 25%; font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 8px;">Evaluadores</th>
                <th class="text-center" style="width: 20%; font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 8px;">Nota</th>
                <th style="width: 25%; font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 8px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historial as $ev)
            <tr>
                <td class="ps-4 fw-medium" style="font-size: 0.8rem;">{{ $ev->fecha_formateada }}</td>
                <td>
                    <span class="badge rounded-pill bg-info text-dark" style="font-size: 0.7rem; padding: 4px 8px;">
                        <i class="fas fa-user-check"></i> {{ $ev->jefes_count }}
                    </span>
                </td>
                <td class="text-center">
                    <strong class="text-primary" style="font-size: 0.85rem;">{{ number_format($ev->puntuacion_consolidada,2) }}</strong>
                </td>
                <td>
                    <span class="text-success" style="font-size: 0.7rem;">
                        <i class="fas fa-check-circle"></i> Procesado
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif

