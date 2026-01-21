@extends('layouts.app')

@section('content')
<div class="forgot-password-wrapper">
    {{-- Logo de fondo como marca de agua --}}
    <img src="{{ asset('images/ihci_logo.jpg') }}" class="logo-bg-rol" alt="Fondo IHCI">

    <div class="container forgot-card-container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-header text-white fw-bold text-center py-3" style="background-color: #003366;">
                        <i class="fa-solid fa-envelope-open-text me-2"></i> RECUPERAR ACCESO
                    </div>

                    <div class="card-body p-4 bg-white text-center">
                        <p class="text-muted small mb-4">
                            Ingresa tu correo electrónico institucional y te enviaremos un enlace para restablecer tu contraseña.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success small py-2 border-0 shadow-sm mb-3">
                                {{ session('status') }}
                            </div>
                        @endif

               @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px;">
        {{ session('error') }}
    </div>
@endif

<form action="{{ route('password.enviar.manual') }}" method="POST">
    @csrf <label>Correo Electrónico:</label>
    <input type="email" name="email" id="email" required placeholder="Ingresa tu correo">
    
    <button type="submit"> Enviar Enlace de Recuperación
    </button>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection