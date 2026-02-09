<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioTipoSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $tipoAnterior;
    public $motivo;

    public function __construct($solicitud, $tipoAnterior, $motivo)
    {
        $this->solicitud = $solicitud;
        $this->tipoAnterior = $tipoAnterior;
        $this->motivo = $motivo;
    }

    public function build()
    {
        return $this->view('emails.cambio_solicitud')
                    ->subject('Notificación de Ajuste en su Solicitud de Permiso');
    }
}
