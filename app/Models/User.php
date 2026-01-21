<?php

// Namespace del modelo
// Esto indica que esta clase pertenece a App\Models
namespace App\Models;

// Clase base que convierte este modelo en AUTENTICABLE (login, sesiones, etc.)
use Illuminate\Foundation\Auth\User as Authenticatable;

// Trait para notificaciones (reset de contraseña, correos, etc.)
use Illuminate\Notifications\Notifiable;

// Facade para encriptar contraseñas
use Illuminate\Support\Facades\Hash;

/**
 * Modelo User
 * Representa a los usuarios del sistema y permite autenticación
 */
class User extends Authenticatable 
{
    // Habilita notificaciones en el modelo
    use Notifiable; 

    /**
     * Nombre de la tabla en la base de datos
     */
    protected $table = 'users';

    /**
     * Campos que se pueden asignar de forma masiva (create, update)
     */
    protected $fillable = [
        'usuario',                // Nombre de usuario para login
        'email',                  // Correo electrónico
        'password',               // Contraseña (se encripta automáticamente)
        'empleado_id',            // Relación con empleado
        'role_id',                // Relación con rol
        'estado',                 // activo / inactivo
        'debe_cambiar_password'   // Flag para forzar cambio de contraseña
    ];

    /**
     * Campos que NO deben mostrarse en arrays o JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Laravel usa este método para enviar el correo
     * de recuperación de contraseña
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(
            new \Illuminate\Auth\Notifications\ResetPassword($token)
        );
    }

    /**
     * RELACIÓN:
     * Un usuario pertenece a un empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * RELACIÓN:
     * Un usuario pertenece a un rol
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * MUTADOR:
     * Encripta automáticamente la contraseña antes de guardarla
     * Evita doble encriptación
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }
} // Fin de la clase User
