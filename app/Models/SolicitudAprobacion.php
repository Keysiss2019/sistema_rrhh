<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAprobacion extends Model
{
    protected $table = 'solicitud_aprobaciones';
    public $timestamps = false;
    protected $fillable = [
        'solicitud_id', 
        'user_id', 
        'firma_id', 
        'rol_nombre', 
        'paso_orden',
       
    ];

    // Relación para sacar la imagen de la firma
    public function firma()
    {
        return $this->belongsTo(Firma::class, 'firma_id');
    }

    // Relación para sacar el nombre del que firmó
    public function user()
    {
     return $this->belongsTo(User::class, 'user_id');
    }

    // En app/Models/Solicitud.php
    public function aprobaciones()
    {
      // Asegúrate de que apunte al modelo correcto y use la llave foránea correcta
      return $this->hasMany(SolicitudAprobacion::class, 'solicitud_id');
    }
}