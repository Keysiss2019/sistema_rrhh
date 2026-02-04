<?php

namespace App\Models; // Define el namespace del modelo dentro de la aplicación (organización lógica del código)

use Illuminate\Database\Eloquent\Model; // Clase base de Eloquent que permite interactuar con la base de datos usando ORM

class Departamento extends Model
{
    // Nombre real de la tabla en la base de datos
    protected $table = 'departamentos';

    
    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'nombre',              // Nombre del departamento
        'descripcion',         // Descripción general del departamento
        'jefe_empleado_id'     // Empleado que actúa como jefe del departamento
    ];

    // Relación: un departamento pertenece a un empleado que es su jefe
    public function jefeEmpleado()
    {
        // jefe_empleado_id -> empleados.id
        return $this->belongsTo(Empleado::class, 'jefe_empleado_id');
    }

    public function empleados()
{
    // Relación para ver todos los trabajadores de este depto
    return $this->hasMany(Empleado::class, 'departamento_id');
}


}
