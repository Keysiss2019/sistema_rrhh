<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
         /* Estilo general del cuerpo del correo */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background: #1a252f; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; background-color: #ffffff; }
        .footer { font-size: 11px; color: #999; margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #2c3e50; padding: 15px; margin: 15px 0; }
        .badge { background: #27ae60; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    /* Contenedor principal centrado */
    <div class="container">
        <div class="header"> <!-- Cabecera con título del correo -->
            <h2 style="margin:0;">Solicitud Aprobada</h2>
        </div>
        
        <div class="content"> <!-- Contenido principal del correo -->
            <!-- Saludo personalizado usando datos del empleado -->
            <p>Estimado(a) <strong>{{ $solicitud->empleado->nombre }} {{ $solicitud->empleado->apellido }}</strong>,</p>
            
            <p>Le informamos que su solicitud de tiempo compensatorio con código <span class="badge">#{{ $solicitud->id }}</span> ha completado satisfactoriamente el flujo de firmas y ha sido <strong>AUTORIZADA</strong>.</p>
            
            <!-- Caja de información con horas acumuladas y horas pagadas si aplica -->
            <div class="info-box">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li><strong>Total de horas acumuladas:</strong> {{ str_replace('.', ':', number_format($solicitud->horas_acumuladas, 2)) }}</li>
                    @if($solicitud->horas_pagadas > 0)
                        <li style="color: #27ae60;"><strong>Horas autorizadas para pago:</strong> {{ str_replace('.', ':', number_format($solicitud->horas_pagadas, 2)) }}</li>
                    @endif
                </ul>
            </div>

            <!-- Nota importante sobre PDF adjunto -->
            <p style="color: #e67e22; font-weight: bold;">📎 Se ha adjuntado a este correo el formato oficial en PDF con las firmas digitales correspondientes para su control personal.</p>
            
           <!-- Firma del departamento -->
            <p>Atentamente,<br>
            <strong>Gestión de Talento Humano (GTH)</strong></p>
        </div>

        <!-- Pie de página con aviso automático y copyright -->
        <div class="footer">
            Este es un mensaje automático generado por el Sistema de Gestión de Horas Extras.<br>
            © {{ date('Y') }} Instituto Hondureño de Cultura Interamericana.
        </div>
    </div>
</body>
</html>