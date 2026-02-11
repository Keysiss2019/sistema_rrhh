<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraExtra extends Model
{
    use HasFactory;

    // Nombre de la tabla en Workbench
    protected $table = 'horas_extras';

    // Campos que se pueden llenar desde el formulario
    protected $fillable = [
        'empleado_id',
        'lugar',
        'solicitado_a',
        'cargo_solicitante',
        'fecha_inicio',
        'fecha_fin',
        'horas_trabajadas',
        'detalle_actividad',
        'observaciones',
        'codigo_formato',
        'estado',
        'aprobado_por',
        'fecha_aprobacion'
    ];

    // Relación: Una hora extra pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // Relación: Una hora extra es aprobada por un usuario (Jefe)
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

     // Visualizar los detalles de las actividades
    public function detalles()
    {
      // El segundo parámetro es la llave foránea en la tabla horas_extras_detalle
      return $this->hasMany(HoraExtraDetalle::class, 'hora_extra_id');
    }
}