<?php

// Definición del namespace de la clase
namespace App\Mail;

// Importación de clases necesarias
use App\Models\User;                   // Modelo User de la aplicación
use Illuminate\Bus\Queueable;          // Permite poner el correo en cola
use Illuminate\Mail\Mailable;          // Clase base para correos
use Illuminate\Queue\SerializesModels; // Permite serializar modelos en la cola

// Definición de la clase Mailable para enviar contraseña temporal
class PasswordTemporalMail extends Mailable
{
    use Queueable, SerializesModels; // Habilita la cola y serialización de modelos

    // Propiedades públicas que estarán disponibles en la vista del correo
    public $usuario;  // Usuario al que se le enviará la contraseña
    public $password; // Contraseña temporal a enviar

    // Constructor que recibe el usuario y la contraseña temporal
    public function __construct(User $usuario, string $password)
    {
        $this->usuario  = $usuario;  // Asigna el usuario a la propiedad
        $this->password = $password; // Asigna la contraseña temporal a la propiedad
    }

    // Método que construye el correo
    public function build()
    {
        return $this->subject('Credenciales de acceso – Contraseña temporal') // Asunto del correo
                    ->view('emails.password_temporal');                     // Vista que se usará para el contenido
    }
}