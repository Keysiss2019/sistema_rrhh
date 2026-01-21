{{-- Extiende la plantilla principal de la aplicación --}}
@extends('layouts.app')

{{-- Sección de contenido principal --}}
@section('content')

{{-- Contenedor principal del login --}}
<div class="login-main-wrapper">

    {{-- Caja del login con sombra --}}
    <div class="login-container shadow-lg">

        {{-- Fila sin espacios entre columnas --}}
        <div class="row g-0">

            {{-- Columna izquierda (solo visible en pantallas medianas o grandes) --}}
            <div class="col-md-6 d-none d-md-flex flex-column align-items-center justify-content-center bg-light-custom p-5">

                {{-- Contenedor del logo --}}
                <div class="logo-circle-bg mb-4">
                    {{-- Logo de la institución --}}
                    <img src="{{ asset('images/ihci_logo.jpg') }}" 
                         alt="Logo IHCI" 
                         class="img-fluid rounded-circle shadow-sm">
                </div>

                {{-- Título de bienvenida --}}
                <h2 class="fw-bold text-dark">¡Bienvenido!</h2>

                {{-- Descripción del sistema --}}
                <p class="text-muted text-center">
                    Sistema de Recursos Humanos <br> IHCI
                </p>
            </div>

            {{-- Columna derecha: formulario de login --}}
            <div class="col-md-6 bg-white p-5 d-flex flex-column justify-content-center">

                {{-- Encabezado del formulario --}}
                <div class="form-header mb-4">
                    <h3 class="fw-bold">Iniciar Sesión</h3>
                </div>

                {{-- Formulario de autenticación --}}
                <form method="POST" action="{{ route('login.post') }}">

                    {{-- Token CSRF para seguridad --}}
                    @csrf
                    
                    {{-- Campo usuario --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">USUARIO</label>

                        {{-- Input group con ícono --}}
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="fa-solid fa-user text-muted"></i>
                            </span>

                            {{-- Campo de texto para usuario --}}
                            <input type="text" 
                                   name="usuario" 
                                   class="form-control bg-light border-0 py-2" 
                                   placeholder="Ingresa tu usuario" 
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    {{-- Campo contraseña --}}
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">CONTRASEÑA</label>

                        {{-- Input group con ícono --}}
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="fa-solid fa-lock text-muted"></i>
                            </span>

                            {{-- Campo password --}}
                            <input type="password" 
                                   name="password" 
                                   class="form-control bg-light border-0 py-2" 
                                   placeholder="••••••••" 
                                   required>
                        </div>
                    </div>

                    {{-- Botón de envío --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-ihci btn-lg shadow-sm">
                            ENTRAR 
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                    {{-- Enlace para recuperación de contraseña --}}
                    <div class="text-center mt-4">
                      <a href="#" class="text-decoration-none small text-muted fw-bold">
                         ¿Olvidaste tu contraseña?
                      </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Fin de la sección de contenido --}}
@endsection
