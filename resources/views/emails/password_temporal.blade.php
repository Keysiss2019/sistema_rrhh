<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"> <!-- Define codificación de caracteres para que soporte acentos y caracteres especiales -->
    <title>Contraseña temporal</title> <!-- Título del correo, se ve en algunas vistas de clientes de email -->
</head>
<body>
    <!-- Saludo personalizado usando el nombre del empleado relacionado al usuario -->
    <h3>Hola {{ $usuario->empleado->nombre }} {{ $usuario->empleado->apellido }},</h3>

    <!-- Mensaje principal indicando que se asignó una contraseña temporal -->
    <p>Se te ha asignado una <strong>contraseña temporal</strong> para acceder al sistema.</p>

    <!-- Datos del usuario y la contraseña temporal -->
    <p><strong>Usuario:</strong> {{ $usuario->usuario }}</p>
    <p><strong>Contraseña temporal:</strong> {{ $password }}</p>

    <!-- Aviso de seguridad indicando que debe cambiar la contraseña después de iniciar sesión -->
    <p>
        ⚠️ Por seguridad, el sistema te pedirá cambiar esta contraseña
        inmediatamente después de iniciar sesión.
    </p>

    <!-- Despedida -->
    <p>Atentamente,<br>
    <strong>Departamento de Tecnología</strong></p>
</body>
</html>
 