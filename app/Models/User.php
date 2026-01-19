<?php

// Namespace del modelo
// Esto indica que esta clase pertenece a App\Models
namespace App\Models;

// Importamos la clase base Model de Eloquent para usar ORM
use Illuminate\Database\Eloquent\Model;

// Importamos Hash para encriptar contraseñas
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    // Especificamos la tabla de la base de datos asociada a este modelo
    protected $table = 'users';

    // Campos que se pueden asignar de forma masiva (mass assignment)
    protected $fillable = [
        'usuario',               // Nombre de usuario
        'password',              // Contraseña encriptada
        'empleado_id',           // Relación con tabla empleados
        'role_id',               // Relación con tabla roles
        'estado',                // Estado del usuario: activo/inactivo
        'debe_cambiar_password'  // Flag que indica si debe cambiar la contraseña al ingresar
    ];

    // RELACIÓN: Un usuario pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // RELACIÓN: Un usuario pertenece a un rol
    public function rol()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Mutador para el atributo "password"
     * Se ejecuta automáticamente cuando se asigna $user->password = 'algo';
     */
    public function setPasswordAttribute($value)
    {
        // Solo encripta si el valor no está ya encriptado
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }
}

