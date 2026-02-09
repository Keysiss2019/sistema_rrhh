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
        'empleado_id',
        'solicitado_a',       
        'cargo_autorizador',  
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
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Relación: Una solicitud es aprobada por un usuario (jefe/RRHH).
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
