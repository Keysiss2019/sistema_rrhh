<?php

namespace App\Models; //Define el espacio de nombres del modelo dentro de la aplicación.

use Illuminate\Database\Eloquent\Model;  //Importación de la clase base Eloquent Model
use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait de Laravel que permite usar factories para crear instancias del modelo (útil para seeders y pruebas)

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'email', 'fecha_nacimiento',
        'fecha_ingreso', 'fecha_baja', 'estado', 'cargo', 'departamento_id'
    ];

    // Relación con departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    // Obtener jefe del departamento
    public function jefe()
    {
        return $this->departamento ? $this->departamento->jefeEmpleado : null;
    }

    // Departamentos donde es jefe
    public function departamentosComoJefe()
    {
        return $this->hasMany(Departamento::class, 'jefe_empleado_id');
    }
}
