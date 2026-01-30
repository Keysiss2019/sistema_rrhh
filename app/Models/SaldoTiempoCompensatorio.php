<?php

// Define el espacio de nombres del modelo
namespace App\Models;

// Importa la clase base Model de Eloquent
use Illuminate\Database\Eloquent\Model;

// Modelo que representa el saldo consolidado de tiempo compensatorio por empleado
class SaldoTiempoCompensatorio extends Model
{
    // Nombre explícito de la tabla en la base de datos
    protected $table = 'saldo_tiempo_compensatorio';

    // Campos que pueden asignarse de forma masiva (mass assignment)
    protected $fillable = [
        'empleado_id',        // ID del empleado dueño del saldo
        'horas_acumuladas',   // Total de horas acumuladas (entradas)
        'horas_usadas',       // Total de horas usadas como tiempo libre
        'horas_pagadas',      // Total de horas pagadas en dinero
        'horas_debe',         // Horas en negativo cuando se excede lo acumulado
        'horas_disponibles'   // Horas disponibles actuales
    ];

    // Relación: un saldo pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
