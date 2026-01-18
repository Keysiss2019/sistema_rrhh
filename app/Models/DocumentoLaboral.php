<?php

namespace App\Models; //Define que esta clase pertenece al espacio de nombres App\Models.

use Illuminate\Database\Eloquent\Model; //Permite que DocumentoLaboral herede todas las funcionalidades de Eloquent:

class DocumentoLaboral extends Model
{
    //Definición de la tabla asociada
    protected $table = 'documentos_laborales'; 

     protected $fillable = [
        'empleado_id',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
    ];
    // Relación: el documento pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
