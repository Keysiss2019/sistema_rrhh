@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="card shadow mb-4">

        {{-- ENCABEZADO IHCI --}}
        <div class="card-header py-4 text-center"
             style="background-color: rgb(63, 81, 243); color: white; border-bottom: 4px solid #d9534f;">

            <div class="d-flex justify-content-between align-items-center px-3">

                <div>
                    <img src="{{ asset('images/ihci_logo.jpg') }}"
                         alt="IHCI"
                         style="max-height: 60px;">
                </div>

                <div>
                    <h2 class="m-0 font-weight-bold"
                        style="letter-spacing: 1px; font-size: 1.5rem;">
                        INSTITUTO HONDUREÑO DE CULTURA INTERAMERICANA
                    </h2>

                    <div class="mt-2">
                        <span class="px-3 py-1"
                              style="border: 1px solid rgba(255,255,255,0.3);
                                     border-radius: 5px;
                                     text-transform: uppercase;
                                     background: rgba(255,255,255,0.1);
                                     font-weight: 500;">
                            {{ $asignacion->formulario }}
                        </span>
                    </div>
                </div>

                <div style="width: 150px;" class="text-right">
                    <a href="javascript:history.back()"
                       class="btn btn-sm btn-danger shadow-sm">
                        <i class="fas fa-arrow-left"></i> Regresar
                    </a>
                </div>

            </div>
        </div>

        <div class="card-body">

            {{-- DATOS GENERALES --}}
            <div class="row mb-4 p-3 bg-light rounded border mx-0 shadow-sm">

                {{-- EVALUADO --}}
                <div class="col-md-6 border-right">
                    <h6 class="text-primary font-weight-bold">
                        <i class="fas fa-user-tie mr-2"></i> DATOS DEL EVALUADO
                    </h6>
                    <hr>

                    <p><strong>Nombre:</strong> {{ $asignacion->nombre_completo_evaluado }}</p>
                    <p><strong>Cargo:</strong> {{ $asignacion->cargo ?? 'N/A' }}</p>
                    <p><strong>Departamento:</strong> {{ $asignacion->nombre_departamento ?? 'N/A' }}</p>
                </div>

                {{-- EVALUADOR --}}
                <div class="col-md-6">
                    <h6 class="text-primary font-weight-bold">
                        <i class="fas fa-clipboard-check mr-2"></i> INFORMACIÓN DEL EVALUADOR
                    </h6>
                    <hr>

                    <p>
                        <strong>Evaluador:</strong>
                        {{ $asignacion->nombre_completo_evaluador ?? 'N/A' }}
                    </p>

                    <p>
                        <strong>Fecha:</strong>
                        {{ \Carbon\Carbon::parse($asignacion->created_at)->format('d/m/Y') }}
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        {{ $asignacion->estado }}
                    </p>
                </div>
            </div>

            {{-- TABLA SOLO LECTURA --}}
            <div class="table-responsive">

                <table class="table table-bordered text-center">

                    <thead class="bg-white text-dark">
                        <tr>
                            <th style="width: 40%;">Criterios de Evaluación</th>
                            <th>{{ $formulario->label_5 ?? 'Superior' }}</th>
                            <th>{{ $formulario->label_4 ?? 'Expectativa' }}</th>
                            <th>{{ $formulario->label_3 ?? 'Cumple' }}</th>
                            <th>{{ $formulario->label_2 ?? 'Mejorar' }}</th>
                            <th>{{ $formulario->label_1 ?? 'No Sat' }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($preguntas as $pregunta)

                            @php
                                $valor = $respuestas[$pregunta->id] ?? null;
                            @endphp

                            <tr>

                                <td class="text-left" style="background-color:#fdf2f5;">
                                    <div class="font-weight-bold">
                                        {{ $pregunta->pregunta }}
                                    </div>

                                    @if($pregunta->categoria)
                                        <div class="small text-muted">
                                            {{ $pregunta->categoria }}
                                        </div>
                                    @endif
                                </td>

                                @for($i = 5; $i >= 1; $i--)

                                    <td class="align-middle">

                                        <input type="radio"
                                               disabled
                                               {{ $valor == $i ? 'checked' : '' }}>

                                    </td>

                                @endfor

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-danger">
                                    No hay preguntas registradas.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>
@endsection