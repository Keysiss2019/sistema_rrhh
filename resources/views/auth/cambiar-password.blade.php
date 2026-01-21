@extends('layouts.app') {{-- Extiende la plantilla principal de la app --}}

@section('content')

<div class="pwd-body"> {{-- Contenedor del fondo y centrado --}}
    <div class="glass-card"> {{-- Tarjeta principal tipo glass --}}
        <div class="card-header-custom"> {{-- Header con icono y título --}}
            <i class="fa-solid fa-shield-halved"></i>
            <h2>SEGURIDAD IHCI</h2>
            <p class="small opacity-75">Actualización de contraseña obligatoria</p>
        </div>

        <div class="card-body p-4"> {{-- Cuerpo de la tarjeta --}}

            {{-- Mensaje de información --}}
            @if(session('info'))
                <div class="alert alert-warning alert-custom">
                    <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                </div>
            @endif

            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-custom">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario para actualizar contraseña --}}
            <form method="POST" action="{{ route('password.actualizar') }}">
                @csrf {{-- Token CSRF obligatorio --}}

                {{-- Campo: Nueva contraseña --}}
                <div class="form-group-custom">
                    <label class="form-label fw-bold mb-2">Nueva Contraseña</label>
                    <i class="fa-solid fa-lock icon-input"></i>
                    <input type="password" name="password" class="input-custom" required minlength="8" placeholder="Mínimo 8 caracteres">
                </div>

                {{-- Campo: Confirmar contraseña --}}
                <div class="form-group-custom">
                    <label class="form-label fw-bold mb-2">Confirmar Contraseña</label>
                    <i class="fa-solid fa-key icon-input"></i>
                    <input type="password" name="password_confirmation" class="input-custom" required placeholder="Repite tu nueva contraseña">
                </div>

                {{-- Botón de envío --}}
                <button type="submit" class="btn-update">
                    ACTUALIZAR CREDENCIALES
                    <i class="fa-solid fa-circle-check ms-2"></i>
                </button>
            </form>
        </div> {{-- Fin card-body --}}
    </div> {{-- Fin glass-card --}}
</div> {{-- Fin pwd-body --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmInput = document.querySelector('input[name="password_confirmation"]');
    const form = document.querySelector('form');

    // Crear contenedor de feedback tipo lista
    const feedback = document.createElement('ul');
    feedback.style.listStyle = 'none';
    feedback.style.paddingLeft = '0';
    feedback.style.fontSize = '14px';
    feedback.style.marginTop = '5px';
    passwordInput.parentNode.appendChild(feedback);

    const rules = [
        { regex: /.{8,}/, text: 'Mínimo 8 caracteres' },
        { regex: /[a-z]/, text: 'Una letra minúscula' },
        { regex: /[A-Z]/, text: 'Una letra mayúscula' },
        { regex: /\d/, text: 'Al menos un número' },
        { regex: /[.!@#$%^&*]/, text: 'Al menos un símbolo (.!@#$%^&*)' }
    ];

    function updateFeedback() {
        const val = passwordInput.value;
        feedback.innerHTML = '';
        rules.forEach(rule => {
            const li = document.createElement('li');
            li.textContent = rule.text;
            if (rule.regex.test(val)) {
                li.style.color = 'green';
                li.textContent = '✅ ' + li.textContent;
            } else {
                li.style.color = 'red';
                li.textContent = '❌ ' + li.textContent;
            }
            feedback.appendChild(li);
        });
    }

    passwordInput.addEventListener('input', updateFeedback);

    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirm = confirmInput.value;

        // Validación final antes de enviar
        const unmet = rules.filter(r => !r.regex.test(password));
        if (unmet.length > 0) {
            e.preventDefault();
            alert('La contraseña no cumple con todos los requisitos.');
            return;
        }
        if (password !== confirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
            return;
        }
    });
});
</script>

@endsection
