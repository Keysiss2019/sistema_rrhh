<?php
// Definimos el namespace de la clase Mailable
namespace App\Mail;

// Importamos las clases necesarias
use Illuminate\Bus\Queueable;             // Permite que los emails se puedan enviar en cola
use Illuminate\Mail\Mailable;             // Clase base para crear correos en Laravel
use Illuminate\Queue\SerializesModels;   // Permite serializar modelos para enviarlos por cola
use App\Models\HoraExtra;                // Modelo que representa la solicitud de horas extra
use Barryvdh\DomPDF\Facade\Pdf;          // Facade de DomPDF para generar PDFs

// Definimos la clase Mailable para enviar el correo de horas extra
class HorasExtraMail extends Mailable
{
    use Queueable, SerializesModels; // Incluimos traits para que el email pueda ser enviado en cola y serializar modelos

    // Propiedades públicas que estarán disponibles en la vista del correo
    public $solicitud;           // Contiene los datos de la solicitud de horas extra
    public $pdfContent;          // Contiene el contenido PDF generado
    public $pasosConfigurados;   // Contiene los pasos de aprobación que se usarán en la vista

    // Constructor de la clase: recibe la solicitud, el PDF y los pasos configurados
    public function __construct(HoraExtra $solicitud, $pdfContent, $pasosConfigurados)
    {
        $this->solicitud = $solicitud;               // Asignamos la solicitud a la propiedad pública
        $this->pdfContent = $pdfContent;             // Asignamos el contenido del PDF
        $this->pasosConfigurados = $pasosConfigurados; // Asignamos los pasos de aprobación
    }

    // Método build: define cómo se construye el correo
    public function build()
    {
        return $this
            ->subject('Solicitud de Tiempo Compensado Finalizada - #' . $this->solicitud->id) // Asunto del correo
            ->view('emails.horas_extra_finalizada')  // Vista Blade que se usará como contenido del correo
            ->attachData(                            // Adjunta el PDF al correo
                $this->pdfContent,                   // Contenido del PDF
                "Solicitud_{$this->solicitud->id}.pdf", // Nombre del archivo PDF adjunto
                [
                    'mime' => 'application/pdf',    // Tipo MIME del archivo
                ]
            );
    }
}