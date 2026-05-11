<?php

// Namespace donde se almacenan las notificaciones del sistema
namespace App\Notifications;


use Illuminate\Bus\Queueable;                       // Trait que permite manejar la notificación mediante colas (queues)
use Illuminate\Contracts\Queue\ShouldQueue;        // Interfaz que indica que la notificación será enviada en segundo plano usando colas
use Illuminate\Notifications\Messages\MailMessage; // Clase utilizada para construir mensajes de correo electrónicos
use Illuminate\Notifications\Notification;        // Clase base para crear notificaciones personalizadas en Laravel

class TareaCorregidaNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
{
    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject('Acción Requerida: Corrección de Tarea')
        ->greeting('Hola ' . $notifiable->usuario)
        ->line('El jefe ha solicitado una corrección en la tarea: ' . $this->tarea->titulo)
        ->line('Observaciones del jefe:')
        ->line('"' . $this->tarea->observaciones_jefe . '"')
        ->action('Ver Tarea', url('/proyectos'))
        ->line('Por favor, realiza los cambios y vuelve a enviar la evidencia.');
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
