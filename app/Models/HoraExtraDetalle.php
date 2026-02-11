<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoraExtraDetalle extends Model
{
    // Especifica la tabla que usa este modelo
    protected $table = 'horas_extras_detalle';

    // Campos que se pueden asignar masivamente (mass assignment)
    protected $fillable = [
        'hora_extra_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'horas_trabajadas',
        'actividad',
    ];
}
