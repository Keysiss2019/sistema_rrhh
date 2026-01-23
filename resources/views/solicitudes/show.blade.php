 <div id="contenedor-principal-show">

    <div id="area-impresion-final" style="background: white; padding: 30px; font-family: Arial, sans-serif; color: black; line-height: 1.6;">

       

        {{-- ENCABEZADO OFICIAL --}}

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 1.5px solid black;">

            <tr>

                <td rowspan="4" style="width: 20%; border: 1px solid black; text-align: center; padding: 10px;">

                    <img src="{{ asset('images/ihci_logo.jpg') }}" style="max-height: 55px;">

                </td>

                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px; width: 45%;">FORMATO</td>

                <td style="border: 1px solid black; text-align: center; font-weight: bold; width: 15%; font-size: 10px;">CÓDIGO</td>

                <td style="border: 1px solid black; text-align: center; width: 20%; font-size: 11px;">FT-GTH-001</td>

            </tr>

            <tr>

                <td rowspan="3" style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">SOLICITUD DE PERMISO</td>

                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 10px;">VERSIÓN</td>

                <td style="border: 1px solid black; text-align: center; font-size: 11px;">2</td>

            </tr>

            <tr>

                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 10px;">VIGENTE DESDE</td>

                <td style="border: 1px solid black; text-align: center; font-size: 11px;">21/2/2024</td>

            </tr>

            <tr>

                <td colspan="2" style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 10px;">PÁGINA 1 DE 1</td>

            </tr>

        </table>



        {{-- CUERPO DE DATOS CON ESPACIOS AMPLIADOS --}}

        <div style="margin-top: 15px; font-size: 12px;">

            <div style="margin-bottom: 18px;">

                <span style="font-weight: bold;">Lugar y fecha:</span>

                <div style="border-bottom: 1px solid black; display: inline-block; width: 75%; padding-left: 10px;">

                    Tegucigalpa, M.D.C. {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y') }}

                </div>

            </div>



            <div style="margin-bottom: 18px;">

                <span style="font-weight: bold;">Solicitante:</span>

                <div style="border-bottom: 1px solid black; display: inline-block; width: 75%; padding-left: 10px;">

                    {{ strtoupper($empleado->nombre) }} {{ strtoupper($empleado->apellido) }}

                </div>

            </div>



            <div style="margin-bottom: 18px;">

                <span style="font-weight: bold;">Cargo del solicitante:</span>

                <div style="border-bottom: 1px solid black; display: inline-block; width: 68%; padding-left: 10px;">

                    {{ strtoupper($empleado->cargo) }}

                </div>

            </div>



            {{-- SALDOS Y TIEMPO CON MÁS AIRE --}}

            <div style="display: flex; margin-bottom: 18px; gap: 20px;">

                <div style="flex: 1;">

                    <span style="font-weight: bold;">Saldo actual vacaciones:</span>

                    <span style="border: 1px solid black; padding: 4px 18px; margin-left: 5px;">{{ $saldoActual }}</span> días

                </div>

                <div style="flex: 1;">

                    <span style="font-weight: bold;">Nuevo saldo:</span>

                    <span style="border: 1px solid black; padding: 4px 18px; margin-left: 5px;">{{ $nuevoSaldo }}</span> días

                </div>

            </div>



            <div style="margin-bottom: 18px;">

                <span style="font-weight: bold;">Solicito se me autorice:</span>

                <span style="border: 1px solid black; padding: 4px 18px; margin-left: 10px;">{{ $solicitud->dias }}</span> días

                <span style="border: 1px solid black; padding: 4px 18px; margin-left: 10px;">{{ $solicitud->horas }}</span> horas

            </div>



           <div style="margin-bottom: 25px;">

              <span style="font-weight: bold; text-transform: uppercase; font-size: 0.9rem;">Fecha(s) del permiso:</span>

              <div style="border-bottom: 1px solid black; display: inline-block; width: 70%; padding-left: 10px; text-align: center; font-weight: bold;">

                  {{-- Añadimos espacios y un separador más claro --}}

                  <span style="letter-spacing: 1px;">

                        DEL &nbsp;&nbsp; {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }}

                        &nbsp;&nbsp;&nbsp; AL &nbsp;&nbsp;&nbsp;

                        {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}

                  </span>

               </div>

            </div>



            {{-- MOTIVOS --}}

            <div style="font-weight: bold; margin-bottom: 12px;">Motivo del permiso:</div>

            <table style="width: 100%; font-size: 11px; margin-bottom: 25px; border-spacing: 0 10px; border-collapse: separate;">

                @php

                    $tipos = [

                        ['vacaciones' => 'A cuenta de vacaciones', 'nupcias' => 'Nupcias'],

                        ['sin_goce' => 'Sin goce de sueldo', 'duelo' => 'Duelo'],

                        ['con_goce' => 'Con goce de sueldo', 'tiempo_compensatorio' => 'A cuenta de tiempo compensatorio'],

                        ['teletrabajo' => 'Teletrabajo', 'otros' => 'Otros (Especifique):']

                    ];

                @endphp

                @foreach($tipos as $fila)

                <tr>

                    @foreach($fila as $key => $label)

                    <td style="width: 50%;">

                        <div style="display: flex; align-items: center;">

                            <div style="width: 32px; height: 26px; border: 1.5px solid black; display: inline-block; text-align: center; line-height: 26px; font-weight: bold; margin-right: 12px;">

                                {{ $solicitud->tipo == $key ? 'X' : '' }}

                            </div>

                            <span>{{ $label }}</span>

                        </div>

                    </td>

                    @endforeach

                </tr>

                @endforeach

            </table>



            <div style="font-weight: bold; margin-bottom: 10px;">Detalles del permiso:</div>

            <div style="border: 1px solid black; width: 100%; min-height: 80px; padding: 12px; margin-bottom: 45px; font-size: 11px;">

                {{ $solicitud->detalles }}

            </div>



            <div style="font-weight: bold; margin-bottom: 10px;">Autorización:</div>



            {{-- FIRMAS CON MUCHO ESPACIO PARA FIRMAR --}}

            <table style="width: 100%; text-align: center; margin-top: 90px; border-collapse: collapse;">

                <tr>

                    <td style="width: 30%; border-top: 1.5px solid black; padding-top: 10px; font-weight: bold; font-size: 11px;">Firma Solicitante</td>

                    <td style="width: 5%;"></td>

                    <td style="width: 30%; border-top: 1.5px solid black; padding-top: 10px; font-weight: bold; font-size: 11px;">Firma Jefe Inmediato</td>

                    <td style="width: 5%;"></td>

                    <td style="width: 30%; border-top: 1.5px solid black; padding-top: 10px; font-weight: bold; font-size: 11px;">V°B° Gestión de TH</td>

                </tr>

            </table>

        </div>



        <div style="margin-top: 60px; font-size: 10px; color: black; font-style: italic; text-align: center;">

            Original: Gestión de Talento Humano / Copia: Expediente colaborador

        </div>

    </div>



    {{-- BOTÓN CON LÓGICA REPARADA E INTEGRADA --}}

    <div style="text-align: right; padding: 15px; background: #f8f9fa; border-top: 1px solid #ddd;" class="no-print">

        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>

        <button type="button" class="btn btn-primary px-4 fw-bold shadow"

            onclick="(function(){

                var content = document.getElementById('area-impresion-final').innerHTML;

                var iframe = document.createElement('iframe');

                iframe.style.position = 'fixed';

                iframe.style.bottom = '0';

                iframe.style.right = '0';

                iframe.style.width = '0';

                iframe.style.height = '0';

                iframe.style.border = '0';

                document.body.appendChild(iframe);

                var doc = iframe.contentWindow.document;

                doc.open();

                doc.write('<html><head><style>body{font-family:Arial; padding:0; margin:0;} @page{size:letter; margin:1.5cm;} table{width:100%; border-collapse:collapse;}</style></head><body>' + content + '</body></html>');

                doc.close();

                iframe.contentWindow.focus();

                setTimeout(function(){

                    iframe.contentWindow.print();

                    document.body.removeChild(iframe);

                }, 600);

            })()">

            <i class="fas fa-print me-2"></i>Imprimir

        </button>

    </div>

</div>