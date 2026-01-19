<?php

namespace App\Models;                    // Define que esta clase pertenece al espacio de nombres App\Models.


use Illuminate\Database\Eloquent\Model;  //Importación de la clase base Eloquent Model. Permite que el modelo RolModulo herede todas las funcionalidades


class RolModulo extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Definición de la tabla asociada
    |--------------------------------------------------------------------------
    | Especifica explícitamente el nombre de la tabla en la base de datos
    | que este modelo representa.
    */
    protected $table = 'rol_modulos';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva (Mass Assignment)
    |--------------------------------------------------------------------------
    | Define los campos que pueden ser asignados de forma masiva
    | mediante métodos como create() o update().
    */
    protected $fillable = [
        'role_id',  // ID del rol al que se le asignan los permisos
        'modulo',   // Nombre del módulo del sistema
        'visible'   // Indica si el módulo es visible (1) o no (0)
    ];

    /*
    |--------------------------------------------------------------------------
    | Relación Eloquent: belongsTo
    |--------------------------------------------------------------------------
    | Define que un registro de RolModulo pertenece a un Rol.
    | Permite acceder al rol asociado desde un módulo:
    | $rolModulo->role
    */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
