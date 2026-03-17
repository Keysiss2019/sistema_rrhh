<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Definición del modelo Firma
class Firma extends Model
{
    // Especifica el nombre de la tabla en la base de datos
    // Laravel normalmente lo deduce automáticamente (plural del modelo),
    // pero aquí se define explícitamente.
    protected $table = 'firmas';

    // Define los campos que pueden ser llenados de forma masiva (mass assignment)
    // Esto permite usar métodos como create() o updateOrCreate()
    protected $fillable = ['empleado_id', 'imagen_path', 'activo'];

    /**
     * RELACIÓN: Firma → Empleado
     * ---------------------------------
     * Indica que cada firma pertenece a un empleado.
     */
    public function empleado()
    {
       
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}