<?php

namespace App\Models;                     // Namespace donde se encuentran los modelos del proyecto

use Illuminate\Database\Eloquent\Model;   // Clase base Model de Laravel


class FormularioPregunta extends Model
{
    protected $table = 'formulario_preguntas'; // Nombre de la tabla asociada al modelo
    protected $fillable = ['formulario_id', 'pregunta', 'categoria'];  // Campos permitidos para asignación

    // ESTO DESACTIVA EL ERROR DE LOS TIMESTAMPS
    public $timestamps = false;
}