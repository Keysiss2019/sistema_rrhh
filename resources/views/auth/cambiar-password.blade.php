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
            <form id="changePasswordForm" method="POST" action="{{ route('password.actualizar') }}">
              @csrf

              <div class="form-group-custom">
                  <label class="form-label fw-bold mb-2">Nueva Contraseña</label>
                  <div class="input-wrapper">
                      <i class="fa-solid fa-lock icon-input"></i>
                      <input type="password" id="password" name="password" class="input-custom" required placeholder="Mínimo 8 caracteres">
                       <i class="fa-solid fa-eye icon-eye" id="togglePassword"></i>
                   </div>
               </div>

              <div class="form-group-custom mt-3">
                  <label class="form-label fw-bold mb-2">Confirmar Contraseña</label>
                  <div class="input-wrapper">
                      <i class="fa-solid fa-key icon-input"></i>
                      <input type="password" id="password_confirmation" name="password_confirmation" class="input-custom" required placeholder="Repite tu nueva contraseña">
                  </div>
             </div>

              <button type="submit" class="btn-update mt-4" id="btnSubmit" disabled>
                  <span id="btnText">ACTUALIZAR CREDENCIALES <i class="fa-solid fa-circle-check ms-2"></i></span>
                   <span id="btnLoader" class="d-none">PROCESANDO... <i class="fa-solid fa-spinner fa-spin ms-2"></i></span>
              </button>
   
            </form>
        </div> {{-- Fin card-body --}}
    </div> {{-- Fin glass-card --}}
</div> {{-- Fin pwd-body --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');

    // Crear la lista de requisitos dinámicamente
    const feedback = document.createElement('ul');
    feedback.className = "password-requirements";
    passwordInput.closest('.form-group-custom').appendChild(feedback);

    const rules = [
        { regex: /.{8,}/, text: 'Mínimo 8 caracteres' },
        { regex: /[A-Z]/, text: 'Una letra mayúscula' },
        { regex: /\d/, text: 'Al menos un número' },
        { regex: /[.!@#$%^&*]/, text: 'Un símbolo (.!@#$%^&*)' }
    ];

    function validate() {
        const val = passwordInput.value;
        const confVal = confirmInput.value;
        feedback.innerHTML = '';
        
        let allRulesMet = true;

        rules.forEach(rule => {
            const isMet = rule.regex.test(val);
            const li = document.createElement('li');
            li.innerHTML = `${isMet ? '✅' : '❌'} ${rule.text}`;
            li.style.color = isMet ? '#198754' : '#dc3545';
            feedback.appendChild(li);
            if (!isMet) allRulesMet = false;
        });

        // Validar coincidencia
        const match = (val === confVal && confVal !== "");
        confirmInput.classList.toggle('is-valid', match);
        confirmInput.classList.toggle('is-invalid', !match && confVal !== "");

        // Habilitar botón solo si todo es correcto
        btnSubmit.disabled = !(allRulesMet && match);
    }

    // Toggle visibilidad
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        confirmInput.type = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    passwordInput.addEventListener('input', validate);
    confirmInput.addEventListener('input', validate);

    // Spinner al enviar
    form.addEventListener('submit', function() {
      btnSubmit.disabled = true;
      document.getElementById('btnText').style.display = 'none'; // Usar style.display es más seguro que d-none a veces
      const loader = document.getElementById('btnLoader');
      loader.classList.remove('d-none');
      loader.style.display = 'inline-block';
   });
});
</script>

@endsection
