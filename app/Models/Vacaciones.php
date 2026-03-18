<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa la tabla de Vacaciones en la base de datos.
 * Esta clase interactúa con los registros de la tabla 'vacaciones'.
 */
class Vacaciones extends Model
{
    // Permite crear datos de prueba (factories) para este modelo
    use HasFactory;

    // Nombre de la tabla en la base de datos que este modelo controla
    protected $table = 'vacaciones';

    /**
     * $fillable: Atributos que se pueden asignar de forma masiva.
     * Esto protege la base de datos permitiendo que solo estos campos 
     * se guarden mediante formularios (como el de tu vista 'create').
     */
    protected $fillable = [
        'empleado_id',      // ID del empleado que solicita (Llave foránea)
        'fecha_inicio',     // Fecha en que comienzan las vacaciones
        'fecha_fin',        // Fecha en que terminan las vacaciones
        'dias_solicitados', // Cantidad de días que el empleado escribió en el formato
        'dias_aprobados',    // Cantidad de días que el jefe realmente autorizó
        'dias_disponibles', // Saldo de vacaciones que tenía el empleado al solicitar
        'estado',           // pendiente, aprobado, rechazado
        'aprobado_por',     // ID del jefe o encargado que firmó/autorizó
        'fecha_aprobacion', // Cuándo se le dio el V°B°
        'creado_por'        // Usuario que registró la solicitud en el sistema
    ];

    /**
     * RELACIÓN: Un registro de Vacaciones pertenece a un Empleado.
     * Esto permite hacer: $vacacion->empleado->nombre en las vistas.
     */
    public function empleado()
    {
        // belongsTo indica que 'empleado_id' es la llave foránea que conecta con la tabla empleados
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}