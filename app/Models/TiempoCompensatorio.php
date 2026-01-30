<?php

/* =========================
   NAMESPACE Y DEPENDENCIAS
========================= */

namespace App\Models; // Namespace del modelo

use Illuminate\Database\Eloquent\Factories\HasFactory; // Soporte para factories
use Illuminate\Database\Eloquent\Model;                // Modelo base de Eloquent

/* =========================
   MODELO TIEMPO COMPENSATORIO
========================= */

class TiempoCompensatorio extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * Nombre real de la tabla en la base de datos
     */
    protected $table = 'tiempo_compensatorio';

    /**
     * Campos que pueden asignarse de forma masiva (Mass Assignment)
     */
    protected $fillable = [
        'empleado_id',       // Empleado al que pertenece el movimiento
        'solicitud_id',      // Solicitud relacionada (si aplica)
        'tipo_movimiento',   // Tipo: entrada / salida / libre / pagado
        'horas',             // Cantidad de horas del movimiento
        'autorizado_por',    // Usuario que autorizó el movimiento
        'fecha_movimiento',  // Fecha del movimiento
        'descripcion',       // Observaciones o motivo
    ];

    /**
     * Casts automáticos de atributos
     */
    protected $casts = [
        // Convierte automáticamente a instancia Carbon
        'fecha_movimiento' => 'datetime',
    ];

    /* =========================
       RELACIONES ELOQUENT
    ========================= */

    /**
     * Relación con el empleado dueño del movimiento
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Relación con el usuario que autorizó el movimiento
     */
    public function autorizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'autorizado_por');
    }

    /**
     * Relación con la solicitud que originó el movimiento (opcional)
     */
    public function solicitud()
    {
        return $this->belongsTo(\App\Models\Solicitud::class, 'solicitud_id');
    }
}
