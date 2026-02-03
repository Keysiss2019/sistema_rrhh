<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function jefe()
    {
        // jefe_empleado_id -> empleados.id
        return $this->belongsTo(Empleado::class, 'jefe_empleado_id');
    }
}
