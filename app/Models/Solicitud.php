<?php

namespace App\Models; // Namespace del modelo dentro de la capa de modelos de la aplicación

use Illuminate\Database\Eloquent\Factories\HasFactory; 
// Trait que permite usar factories para generar datos de prueba (seeders, tests)

use Illuminate\Database\Eloquent\Model; 
// Clase base de Eloquent que habilita el ORM para interactuar con la base de datos


class Solicitud extends Model
{
    use HasFactory;

    // Indicamos el nombre exacto de la tabla que creaste en Workbench
    protected $table = 'solicitudes';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombre',
        'solicitado_a',       
        'lugar',  
        'departamento',
        'correo',
        'tipo',
        'motivo_otro',
        'detalles',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'horas',
        'estado',
        'aprobado_por',
        'fecha_aprobacion',
        'dias_anuales_aplicados'
    ];

    /**
     * Relación: Una solicitud pertenece a un empleado.
     */
     public function empleado()
    {
     /**
     * Explicación de los parámetros:
     * 1. Empleado::class: El modelo con el que se relaciona.
     * 2. 'correo': El nombre de la columna en tu tabla 'solicitudes'.
     * 3. 'email': El nombre de la columna en tu tabla 'empleados'.
     */
       return $this->belongsTo(Empleado::class, 'correo', 'email');
    }


    public function firma()
    {
      // Relación 1 a 1: El empleado tiene una firma en la tabla 'firmas'
      return $this->hasOne(Firma::class, 'empleado_id');
    }

    /**
     * Relación: Una solicitud es aprobada por un usuario (jefe/RRHH).
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function aprobaciones()
    {
     // Esta es la relación dinámica que usaremos ahora
      return $this->hasMany(SolicitudAprobacion::class, 'solicitud_id')->orderBy('paso_orden', 'asc');
    }

    // 1. La firma del que crea la solicitud (esta es fija del empleado)
    public function firma_empleado()
    {
     return $this->belongsTo(Firma::class, 'firma_empleado_id');
    }

}