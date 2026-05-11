<?php

// Namespace del modelo
namespace App\Models;

// Importa la clase base Model de Laravel
use Illuminate\Database\Eloquent\Model;

// Definición del modelo
class ProyectoDesignado extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'proyecto_designados';

    // Desactiva timestamps (created_at, updated_at)
    // porque la tabla no los tiene definidos en SQL
    public $timestamps = false; 

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'proyecto_id',   // ID del proyecto asignado
        'user_id',       // ID del usuario asignado
        'es_encargado'   // Indica si puede editar (1) o solo ver (0)
    ];

    /**
     * RELACIÓN: ProyectoDesignado → Usuario
     * -------------------------------------
     * Indica que esta asignación pertenece a un usuario.
     */
    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }
}