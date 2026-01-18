<?php

namespace App\Models; //Define el espacio de nombres del modelo dentro de la aplicación.

use Illuminate\Database\Eloquent\Model;  //Importación de la clase base Eloquent Model

class Empleado extends Model
{
    // Se indica explícitamente la tabla relacionada
    protected $table = 'empleados';

    // Campos que se pueden llenar masivamente (Mass Assignment)
    protected $fillable = [
        'user_id',           // Referencia al usuario (login)
        'nombre',            // Nombre del empleado
        'apellido',          // Apellido del empleado
        'email',             // Correo electrónico
        'fecha_nacimiento',  // Fecha de nacimiento
        'fecha_ingreso',     // Fecha de inicio de contrato
        'fecha_baja',        // Fecha de baja, si aplica
        'estado',            // activo / inactivo
        'cargo',             // Cargo del empleado
        'departamento',      // Departamento
        'jefe_inmediato',  // Referencia al jefe inmediato
        'tipo_contrato',   // Referencia tipo de contrato
        'contacto'        // Referencia de contacto
    ];

    // Relación con usuario
    public function user()
    {
        // Un empleado pertenece a un usuario (login)
        return $this->belongsTo(User::class);
    }

    // Relación con jefe inmediato
    public function jefe_inmediato()
    {
        // Un empleado puede tener un jefe inmediato dentro de la misma tabla
        return $this->belongsTo(Empleado::class, 'jefe_inmediato_id');
    }

    // Relación con empleados a cargo
    public function subordinados()
    {
        // Un empleado puede tener varios subordinados
        return $this->hasMany(Empleado::class, 'jefe_inmediato_id');
    }

    // Relación: un empleado tiene muchos documentos laborales
    public function documentos()
    {
        return $this->hasMany(DocumentoLaboral::class, 'empleado_id');
    }
    
    // Relación con solicitudes (si aplica)
   public function solicitudes()
    {
        // Un empleado puede tener varias solicitudes
        return $this->hasMany(Permiso::class); // Debes tener el modelo Solicitud
    }

    // Relación: un empleado pertenece a una política de vacaciones
    public function politicaVacaciones()
    {
     // Un empleado tiene una sola política de vacaciones
     return $this->belongsTo(PoliticaVacaciones::class);
    }

}
