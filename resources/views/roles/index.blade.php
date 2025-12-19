<!DOCTYPE html>
<html>
<head>
    <title>Roles - IHCI</title>
    <!-- Vinculamos el CSS externo -->
    <link rel="stylesheet" href="{{ asset('css/roles.css') }}">
</head>
<body>

<div class="container">

    {{-- Header con logo y título --}}
    <div class="header">
        {{-- Logo a la izquierda --}}
        <img src="{{ asset('images/ihci_logo.jpg') }}" alt="IHCI Logo" class="logo">

        {{-- Título a la derecha del logo --}}
        <h2>Listado de Roles</h2>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    {{-- Botón Nuevo Rol con símbolo "+" --}}
    <a href="{{ route('roles.create') }}" class="btn-nuevo">+ Nuevo Rol</a>

    {{-- Tabla de roles --}}
    <table class="roles-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $rol)
            <tr>
                <td>{{ $rol->id }}</td>
                <td>{{ $rol->nombre }}</td>
                <td>{{ $rol->descripcion }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

</body>
</html>
