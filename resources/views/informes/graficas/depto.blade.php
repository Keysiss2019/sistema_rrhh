@extends('layouts.app')

@section('content')

{{-- =========================================================
     ESTILOS PERSONALIZADOS
========================================================== --}}
<style>

    /*
    |--------------------------------------------------------------------------
    | Contenedor principal de la gráfica
    |--------------------------------------------------------------------------
    */
    .grafico-container {
        position: relative;
        height: 500px;
        width: 100%;
        background-color: #141820 !important;
        border-radius: 15px;
        padding: 30px;
        border: 1px solid #d1d3e2;
    }

    /*
    |--------------------------------------------------------------------------
    | Canvas de la gráfica
    |--------------------------------------------------------------------------
    */
    #canvasDepto {
        background-color: rgba(0,0,0,0) !important;
    }

    /*
    |--------------------------------------------------------------------------
    | Contenedor de checkboxes
    |--------------------------------------------------------------------------
    */
    .checkbox-group {
        background-color: #ffffff;
        border: 1px solid #d1d3e2;
        border-radius: 5px;
        padding: 10px;
        max-height: 150px;
        overflow-y: auto;
    }

    /*
    |--------------------------------------------------------------------------
    | Cursor para labels
    |--------------------------------------------------------------------------
    */
    .custom-control-label {
        cursor: pointer;
    }
</style>

{{-- =========================================================
     CONTENEDOR PRINCIPAL
========================================================== --}}
<div class="container-fluid">

    {{-- =========================================================
         TÍTULO PRINCIPAL
    ========================================================== --}}
    <div class="row mb-4">

        <div class="col-12 text-center">

            <h2 class="font-weight-bold text-dark">

                <i class="fas fa-chart-bar text-primary mr-2"></i>

                Comparativa de Desempeño por Departamento
            </h2>

            <hr>
        </div>
    </div>

    {{-- =========================================================
         TARJETA PRINCIPAL
    ========================================================== --}}
    <div class="card shadow mb-4">

        {{-- =========================================================
             CUERPO DE LA TARJETA
        ========================================================== --}}
        <div class="card-body" style="background-color: #f8f9fc;"> 

            {{-- =========================================================
                 FILTROS
            ========================================================== --}}
            <div class="row align-items-end mb-4">

                {{-- =========================================================
                     CHECKBOXES DE DEPARTAMENTOS
                ========================================================== --}}
                <div class="col-md-4">

                    <label class="font-weight-bold text-gray-800">
                        Departamentos a Comparar:
                    </label>

                    {{-- Contenedor scrollable --}}
                    <div class="checkbox-group">

                        {{-- Recorrido de departamentos --}}
                        @foreach($departamentos as $depto)

                            <div class="custom-control custom-checkbox mb-1">

                                {{-- Checkbox --}}
                                <input 
                                    type="checkbox"
                                    class="custom-control-input depto-check"
                                    id="depto_{{ $depto->id }}"
                                    value="{{ $depto->id }}"
                                >

                                {{-- Nombre del departamento --}}
                                <label 
                                    class="custom-control-label text-gray-800"
                                    for="depto_{{ $depto->id }}"
                                >
                                    {{ $depto->nombre }}
                                </label>
                            </div>

                        @endforeach
                    </div>
                </div>

                {{-- =========================================================
                     SELECTOR DE AÑO
                ========================================================== --}}
                <div class="col-md-2">

                    <label class="font-weight-bold text-gray-800">
                        Año:
                    </label>
                   
                    <div id="anio_check_container" style="display:none;">
                       @foreach($anios as $anio)
                          <label>
                             <input type="checkbox" class="anio-check" value="{{ $anio }}">
                             {{ $anio }}
                          </label>
                       @endforeach
                    </div>

                    <select id="anio_valor" class="form-control">
                       <option value="" selected disabled>Elija...</option>
                        @foreach($anios as $anio)
                           <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    </select>
                   
                </div>

                {{-- =========================================================
                     SELECTOR DE MES
                ========================================================== --}}
                <div class="col-md-3">

                    <label class="font-weight-bold text-gray-800">
                        Mes:
                    </label>

                    <select id="mes_valor" class="form-control">

                        <option value="" selected disabled>
                            Elija...
                        </option>

                        {{-- Opción acumulada --}}
                        <option value="">
                            Todo el Año (Acumulado)
                        </option>

                        {{-- Lista de meses --}}
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>

                {{-- =========================================================
                     BOTÓN GENERAR VISUALIZACIÓN
                ========================================================== --}}
                <div class="col-md-3">

                    <button 
                        class="btn btn-primary btn-block shadow"
                        onclick="cargarDatosGrafica()"
                    >

                        <i class="fas fa-sync-alt mr-2"></i>

                        Generar Gráfica
                    </button>
                </div>
            </div>

            {{-- =========================================================
                 ÁREA DE LA GRÁFICA
            ========================================================== --}}
            <div class="row mt-4">

                <div class="col-12">

                    {{-- Contenedor visual --}}
                    <div class="grafico-container">

                        {{-- Canvas Chart.js --}}
                        <canvas id="canvasDepto"></canvas>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     SCRIPT PRINCIPAL
========================================================== --}}

<script>

let miGrafica = null;

function cargarDatosGrafica() {

    const depto_ids = Array.from(
        document.querySelectorAll('.depto-check:checked')
    ).map(cb => cb.value);

    const esUnDepto = depto_ids.length === 1;
    const esMultiDepto = depto_ids.length > 1;

   const mesEl = document.getElementById('mes_valor');
   const mes = mesEl.value;
   const esTodoAnio = mes === "";

    

    let anios = [];

    // 🔵 CASO 1: 1 DEPARTAMENTO → CHECKBOX
    if (esUnDepto) {

        anios = Array.from(
            document.querySelectorAll('.anio-check:checked')
        ).map(cb => cb.value);

        if (anios.length < 2) {
            Swal.fire(
                'Atención',
                'Debe seleccionar al menos 2 años para comparar este departamento',
                'warning'
            );
            return;
        }

    } 
    // 🔴 CASO 2: VARIOS DEPARTAMENTOS → SELECT
    else {

        const anio = document.getElementById('anio_valor').value;

        if (!anio) {
            Swal.fire(
                'Atención',
                'Seleccione un año para comparar departamentos',
                'warning'
            );
            return;
        }

        anios = [anio];
    }

    // 🔴 DEPARTAMENTOS
    if (depto_ids.length === 0) {
        Swal.fire('Atención', 'Seleccione al menos un departamento', 'warning');
        return;
    }

    // 🔴 MES
   // 🔴 VALIDACIÓN CORRECTA DEL MES
if (mesEl.selectedIndex === 0) {
    Swal.fire('Atención', 'Debe seleccionar un mes o "Todo el año"', 'warning');
    return;
}

    Swal.fire({
        title: 'Generando gráfica...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    let url = "{{ route('graficas.data.depto') }}";

    url += '?' + depto_ids
        .map(id => `departamento_ids[]=${encodeURIComponent(id)}`)
        .join('&');

    url += '&' + anios
        .map(a => `anios[]=${encodeURIComponent(a)}`)
        .join('&');

    if (!esTodoAnio) {
        url += `&mes=${encodeURIComponent(mes)}`;
    }

    fetch(url)
        .then(r => r.json())
        .then(data => {

            Swal.close();

            const ctx = document.getElementById('canvasDepto').getContext('2d');

            if (miGrafica) miGrafica.destroy();

            Chart.register(ChartDataLabels);

            miGrafica = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.close();
            Swal.fire('Error', 'No se pudo cargar la gráfica', 'error');
        });
}
</script>
@endsection