<?php

namespace App\Models;  //Define la ubicación lógica del modelo dentro de la aplicación.

// Importación de clases base
use Illuminate\Database\Eloquent\Model; //Se importa la clase Model de Eloquent

class Role extends Model
{
    // Se indica explícitamente la tabla relacionada
    protected $table = 'roles';

    // Campos que se pueden llenar masivamente (Mass Assignment)
    protected $fillable = [
        'nombre',       // Nombre del rol (Ej: Administrador, Empleado)
        'descripcion'   // Descripción del rol
    ];

    // Relación con usuarios
    public function users()
    {
        // Un rol puede tener muchos usuarios
        return $this->hasMany(User::class);
    }
   

}
