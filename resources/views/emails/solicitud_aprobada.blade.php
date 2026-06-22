<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { background-color: #0056b3; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Solicitud Aprobada</h2>
        </div>
        
        <div class="content">
            <p>Hola,</p>
            <p>Te informamos que tu solicitud ha sido <strong>aprobada satisfactoriamente</strong> por el departamento de GTH.</p>

            <div class="details">
                <p><strong>Detalles de la solicitud:</strong></p>
                <ul style="list-style: none; padding: 0;">
                    <li><strong>Tipo:</strong> {{ ucfirst(str_replace('_', ' ', $solicitud->tipo)) }}</li>
                    <li><strong>Periodo:</strong> {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}</li>
                    <li><strong>Días:</strong> {{ $solicitud->dias }}</li>
                </ul>
            </div>

            <p>Puedes verificar el estatus y descargar el formato oficial ingresando al sistema:</p>
            <center>
                <a href="{{ url('/solicitudes/' . $solicitud->id) }}" class="btn">Ver Solicitud</a>
            </center>
        </div>

        <div class="footer">
            <p>Este es un correo automático generado por el sistema de solicitudes de RRHH.</p>
            <p>Por favor, no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>