<?php

namespace App\Models;                     // Namespace donde se encuentran los modelos del proyecto

use Illuminate\Database\Eloquent\Model;   // Clase base Model de Laravel

class EvaluacionRespuesta extends Model
{
    // Nombre de la tabla que creaste en Workbench
    protected $table = 'evaluacion_respuestas';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'asignacion_id',
        'pregunta_id',
        'valor'
    ];

    // Relación: Una respuesta pertenece a una asignación
    public function asignacion()
    {
        return $this->belongsTo(AsignacionEvaluacion::class, 'asignacion_id');
    }
}