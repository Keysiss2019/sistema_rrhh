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
        'horas_acumuladas',
        'horas_pagadas',
        'detalle_actividad',
        'observaciones_jefe',
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

    // Departamento que debe aprobar
    public function departamentoAprobador()
    {
        return $this->belongsTo(Departamento::class, 'departamento_aprobador_id');
    }

     // Visualizar los detalles de las actividades
    public function detalles()
    {
      // El segundo parámetro es la llave foránea en la tabla horas_extras_detalle
      return $this->hasMany(HoraExtraDetalle::class, 'hora_extra_id');
    }

     /**
     * Este método se activa automáticamente al crear una nueva solicitud.
     * Busca al empleado por nombre y le asigna su ID real.
     */
    protected static function booted()
    {
        static::creating(function ($horaExtra) {
            // Si el ID viene vacío pero el nombre trae texto (como pasa con el Form de 365)
            if (empty($horaExtra->empleado_id) && !empty($horaExtra->nombre)) {
                
                // Buscamos al empleado en la tabla 'empleados' por su nombre completo
                $empleado = \App\Models\Empleado::where(\DB::raw("CONCAT(nombre, ' ', apellido)"), 'LIKE', '%' . $horaExtra->nombre . '%')
                    ->orWhere('nombre', 'LIKE', '%' . $horaExtra->nombre . '%')
                    ->first();

                if ($empleado) {
                    // Si lo encuentra, le pone el ID (ej. 128) automáticamente antes de guardar
                    $horaExtra->empleado_id = $empleado->id;
                }
            }
        });
    }

    // Horas pendientes para un departamento
    public function scopePendientesPorDepartamento($query, $departamentoId)
    {
        return $query->where('estado', 'pendiente')
                     ->where('departamento_aprobador_id', $departamentoId);
    }
}