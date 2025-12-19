<!DOCTYPE html>
<html>
<head>
    <title>Crear Rol - IHCI</title>
    <!-- Vinculamos el CSS externo -->
    <link rel="stylesheet" href="{{ asset('css/roles1.css') }}">
</head>
<body>

<div class="container">

    {{-- Logo IHCI --}}
    <img src="{{ asset('images/ihci_logo.jpg') }}" alt="IHCI Logo" class="logo">

    {{-- Título de la página --}}
    <h2>Nuevo Rol</h2>

    {{-- Mostrar errores de validación --}}
    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para crear un nuevo rol --}}
    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        {{-- Campo para nombre del rol --}}
        <label>Nombre:</label>
        <input type="text" name="nombre">

        {{-- Campo para descripción del rol --}}
        <label>Descripción:</label>
        <textarea name="descripcion" rows="4"></textarea>

        {{-- Botón Guardar rol (azul con símbolo "+") --}}
        <button type="submit" class="btn-nuevo"> Guardar</button>

        {{-- Botón Cancelar (gris) --}}
        <a href="{{ route('roles.index') }}" class="btn-cancelar">Cancelar</a>
    </form>

</div>

</body>
</html>
