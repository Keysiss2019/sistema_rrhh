
<h3>Notificación de Cambio en Solicitud</h3>

<p>Hola {{ $solicitud->empleado->nombre }},</p>

<p>Se ha realizado un cambio en tu solicitud.</p>

<ul>
    <li><strong>Tipo anterior:</strong> {{ ucfirst($tipoAnterior) }}</li>
    <li><strong>Nuevo tipo:</strong> {{ ucfirst($solicitud->tipo) }}</li>
    <li><strong>Motivo:</strong> {{ $motivo }}</li>
</ul>

<p>Si tienes dudas, comunícate con RRHH.</p>
