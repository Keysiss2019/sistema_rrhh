@extends('layouts.app')


@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Gestión de Horas Extra</h3>
        <a href="{{ route('configuracion.firmas') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-gear me-1"></i> Configurar Flujo de Firmas
        </a>
    </div>
{{-- --- BLOQUE DE MENSAJES AQUÍ --- --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 auto-close" role="alert" style="border-radius: 10px; border-left: 5px solid #198754;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fa-lg"></i>
                <div>
                    <strong class="d-block">¡Acción exitosa!</strong>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 auto-close" role="alert" style="border-radius: 10px; border-left: 5px solid #dc3545;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                <div>
                    <strong>Hubo un problema:</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- --- FIN BLOQUE DE MENSAJES --- --}}
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">

        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="ps-4">Colaborador</th>
                    <th class="text-center">Ruta de Firmas</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    <tr>
                        <td class="ps-4">
                          @if($solicitud->empleado)
                               {{-- Muestra Nombre + Apellido en Mayúsculas --}}
                               <strong>{{ strtoupper($solicitud->empleado->nombre . ' ' . $solicitud->empleado->apellido) }}</strong>
                            @else
                               {{-- Si el ID es NULL, muestra el texto que cayó en la columna 'nombre' --}}
                               <span class="text-danger">
                                 <strong>{{ strtoupper($solicitud->nombre ?? 'Empleado no vinculado') }}</strong>
                                 <br>
                                 <small>(ID: {{ $solicitud->getRawOriginal('empleado_id') ?? 'NULL' }})</small>
                                </span>
                            @endif
                            <br><small class="text-muted">{{ $solicitud->horas_trabajadas }} hrs</small>
                        </td>
                        <td class="text-center">
                            {{-- CONTENEDOR DE BOLITAS --}}
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                @foreach($pasosConfigurados as $paso)
                                    @php
                                      // Sincronizamos con el valor real de la BD (0, 1, 2...)
                                      $pasoActualSoli = intval($solicitud->paso_actual ?? 0); 
    
                                       // Usamos el índice del loop para comparar
                                       $esCompletado = $pasoActualSoli > $loop->index;
                                       $esActual = $pasoActualSoli == $loop->index;

                                       // Si la solicitud ya está aprobada, todos se marcan como completados
                                       if ($solicitud->estado == 'aprobado') {
                                           $esCompletado = true;
                                           $esActual = false;
                                        }

                                       $bgColor = $esCompletado ? '#198754' : ($esActual ? '#ffc107' : '#e9ecef');
                                       $textColor = ($esCompletado || $esActual) ? 'white' : '#6c757d';
                                       $icono = $esCompletado ? 'fa-check' : ($esActual ? 'fa-pen-nib' : 'fa-lock');
                                    @endphp

                                    <div class="text-center" style="width: 60px;" title="{{ $paso->nombre_paso }}">
                                        <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-1 border" 
                                             style="width: 30px; height: 30px; background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>;">
                                            <i class="fa-solid {{ $icono }}" style="font-size: 0.7rem;"></i>
                                        </div>
                                        <span style="font-size: 0.6rem;" class="text-uppercase fw-bold text-muted d-block">
                                            {{ $paso->nombre_corto }}
                                        </span>
                                    </div>

                                    @if(!$loop->last)
                                        <i class="fa-solid fa-chevron-right small opacity-25"></i>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="text-center">
                           {{-- EL BOTÓN DEBE TENER EL ID DE LA SOLICITUD --}}
                          <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modal-{{ $solicitud->id }}">
                             <i class="fa fa-eye"></i> Revisar
                           </button>
                
                           {{-- INCLUIR EL MODAL AQUÍ MISMO PARA QUE TENGA ACCESO A $solicitud --}}
                            @include('horas_extras.modal_revisar', ['solicitud' => $solicitud])
                        </td>
                   </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-5 text-muted">No hay registros para mostrar</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

{{-- SCRIPT PARA AUTO-CERRAR --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Busca todas las alertas con la clase auto-close
        const autoCloseAlerts = document.querySelectorAll('.auto-close');
        
        autoCloseAlerts.forEach(function(alert) {
            setTimeout(function() {
                // Desvanecer usando Bootstrap
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000); // 5 segundos
        });
    });
</script>
@endsection