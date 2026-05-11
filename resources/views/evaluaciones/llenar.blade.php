@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow mb-4">
        {{-- Encabezado Estilo Institucional IHCI --}}
        <div class="card-header py-4 text-center" style="background-color: rgb(63, 81, 243); color: white; border-bottom: 4px solid #d9534f;">
            <div class="d-flex justify-content-between align-items-center px-3">
                <div class="logo-nav">
                    <img src="{{ asset('images/ihci_logo.jpg') }}" alt="IHCI" style="max-height: 60px;">
                </div>
                
                <div>
                    <h2 class="m-0 font-weight-bold" style="letter-spacing: 1px; font-size: 1.5rem;">
                      INSTITUTO HONDUREÑO DE CULTURA INTERAMERICANA
                   </h2>
                    <div class="mt-2">
                        <span class="px-3 py-1" style="border: 1px solid rgba(255,255,255,0.3); border-radius: 5px; text-transform: uppercase; background: rgba(255,255,255,0.1); font-weight: 500;">
                            {{ $formulario->nombre }}
                        </span>
                    </div>
                </div>

                <div style="width: 150px;" class="text-right">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger shadow-sm">
                        <i class="fas fa-arrow-left"></i> Regresar
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            {{-- Sección de Datos Generales --}}
            <div class="row mb-4 p-3 bg-light rounded border mx-0 shadow-sm">
                {{-- Datos del Evaluado --}}
                <div class="col-md-6 border-right">
                    <h6 class="text-primary font-weight-bold"><i class="fas fa-user-tie mr-2"></i>DATOS DEL EVALUADO</h6>
                    <hr>
                    <p class="mb-1"><strong>Nombre:</strong> {{ $asignacion->nombre_completo_evaluado }}</p>
                    <p class="mb-1"><strong>Cargo:</strong> {{ $asignacion->cargo ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Depto:</strong> {{ $asignacion->nombre_departamento ?? 'N/A' }}</p>
                </div>

                {{-- Información del Evaluador --}}
                <div class="col-md-6">
                    <h6 class="text-primary font-weight-bold"><i class="fas fa-clipboard-check mr-2"></i>INFORMACIÓN DEL EVALUADOR</h6>
                    <hr>
                   <p class="mb-1">
                     <strong>Evaluador:</strong> 
                       {{ $asignacion->nombre_completo_evaluador ?? 'Evaluador no asignado' }}
                    </p>
                    <p class="mb-1"><strong>Fecha de Evaluación:</strong> {{ date('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Estado:</strong> <span class="badge badge-info" style="color:white !important;">MODO LLENADO</span></p>
                </div>
            </div>

            {{-- Formulario de Evaluación --}}
            <form action="{{ route('evaluaciones.guardar') }}" method="POST" id="formEvaluacion">
                @csrf
                <input type="hidden" name="asignacion_id" value="{{ $asignacion->id }}">

                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-white text-dark">
                            <tr>
                                <th style="width: 40%; vertical-align: middle;">Criterios de Evaluación</th>
                                <th style="width: 10%;">{{ $formulario->label_5 ?? 'Superior' }}</th>
                                <th style="width: 10%;">{{ $formulario->label_4 ?? 'Expectativa'}}</th>
                                <th style="width: 10%;">{{ $formulario->label_3 ?? 'Cumple' }}</th>
                                <th style="width: 10%;">{{ $formulario->label_2 ?? 'Mejorar' }}</th>
                                <th style="width: 10%;">{{ $formulario->label_1 ?? 'No Sat' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($preguntas as $pregunta)
                            <tr>
                                <td class="text-left" style="background-color: #fdf2f5; vertical-align: middle;">
                                    <div class="font-weight-bold">{{ $pregunta->pregunta }}</div>
                                    @if($pregunta->categoria)
                                        <div class="small text-muted">{{ $pregunta->categoria }}</div>
                                    @endif
                                </td>
                                @for($i = 5; $i >= 1; $i--)
                                <td class="align-middle">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="p{{ $pregunta->id }}_{{ $i }}" name="respuestas[{{ $pregunta->id }}]" value="{{ $i }}" class="custom-control-input" required>
                                        <label class="custom-control-label" for="p{{ $pregunta->id }}_{{ $i }}"></label>
                                    </div>
                                </td>
                                @endfor
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-danger">No hay preguntas configuradas para este formulario.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-save mr-2"></i> Guardar Evaluación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Logrado!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#003366',
            timer: 3000
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Hubo un problema',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d9534f'
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formulario = document.getElementById('formEvaluacion');
        
        if(formulario) {
            formulario.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Confirmar envío?',
                    text: "Una vez guardada, no podrá modificar la evaluación del IHCI.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#003366',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }
    });
</script>
@endsection