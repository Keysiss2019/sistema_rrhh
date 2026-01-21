@extends('layouts.app') 
{{-- Extiende el layout principal de la aplicación --}}

@section('content')
<div class="pwd-update-wrapper">
    
    {{-- Logo de fondo como marca de agua (identidad visual IHCI) --}}
    <img src="{{ asset('images/ihci_logo.jpg') }}" class="logo-bg-rol" alt="Fondo IHCI">

    {{-- Contenedor principal centrado --}}
    <div class="container" style="z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-md-5">

                {{-- Tarjeta principal del formulario --}}
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                    {{-- Encabezado de seguridad --}}
                    <div class="card-header text-white fw-bold text-center py-3" style="background-color: #003366;">
                        <i class="fa-solid fa-shield-keyhole me-2"></i> 
                        SEGURIDAD: ACTUALIZAR CLAVE
                    </div>
                    
                    {{-- Cuerpo del formulario --}}
                    <div class="card-body p-4">

                        {{-- Mensaje informativo obligatorio --}}
                        <div class="alert alert-warning small border-0 shadow-sm mb-4">
                            <i class="fa-solid fa-circle-exclamation me-1"></i> 
                            Por seguridad, debes cambiar la contraseña temporal para poder acceder al menú principal.
                        </div>
                        
                        {{-- Formulario para actualizar contraseña --}}
                        <form method="POST" action="{{ route('password.actualizar') }}">
                            @csrf {{-- Token de seguridad --}}

                            {{-- Campo: nueva contraseña --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">
                                    NUEVA CONTRASEÑA
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-solid fa-lock text-muted"></i>
                                    </span>
                                    <input type="password"
                                           name="password"
                                           class="form-control border-start-0"
                                           required
                                           minlength="6"
                                           placeholder="Escribe tu nueva clave">
                                </div>
                            </div>

                            {{-- Campo: confirmar contraseña --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">
                                    CONFIRMAR CONTRASEÑA
                                </label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fa-solid fa-check-double text-muted"></i>
                                    </span>
                                    <input type="password"
                                           name="password_confirmation"
                                           class="form-control border-start-0"
                                           required
                                           placeholder="Repite la clave">
                                </div>
                            </div>

                            {{-- Botón de envío --}}
                            <div class="d-grid">
                                <button type="submit"
                                        class="btn btn-lg fw-bold text-white shadow"
                                        style="background-color: #003366; transition: 0.3s;">
                                    ACTUALIZAR Y CONTINUAR 
                                    <i class="fa-solid fa-chevron-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                        {{-- Fin del formulario --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection {{-- Fin de la sección de contenido --}}
