<?php

// Esto permite organizar y autoload las clases correctamente
namespace App\Models;

// Importa la clase base Model de Eloquent
// Todos los modelos de Laravel extienden de esta clase para usar ORM, relaciones y consultas
use Illuminate\Database\Eloquent\Model;

class PoliticaVacaciones extends Model
{
    /**
     * Nombre real de la tabla en la base de datos
     */
    protected $table = 'politicas_vacaciones';

    /**
     * Campos que se pueden insertar / actualizar masivamente
     */
    protected $fillable = [
        'tipo_contrato',
        'dias_anuales'
    ];
}

