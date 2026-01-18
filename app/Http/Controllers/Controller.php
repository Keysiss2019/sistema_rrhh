<?php

/*
 Namespace del Controlador Base
*/
namespace App\Http\Controllers; //Define la ubicación lógica del controlador dentro de la aplicación.

/*
 Importación de Traits y clases base
*/
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;   // Manejo de autorizaciones (políticas y gates)
use Illuminate\Foundation\Validation\ValidatesRequests;     // Funcionalidades de validación de formularios
use Illuminate\Routing\Controller as BaseController;        // Controlador base del framework Laravel

/*
 Clase Controller
*/
class Controller extends BaseController
{
    /*
     Traits utilizados por el controlador
    */
    use AuthorizesRequests, ValidatesRequests;
}
