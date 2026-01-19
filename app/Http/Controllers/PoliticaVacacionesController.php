<?php

// Indica que esta clase pertenece al espacio de nombres App\Http\Controllers
namespace App\Http\Controllers;

// Importa el modelo PoliticaVacaciones para poder interactuar con la tabla correspondiente
use App\Models\PoliticaVacaciones;

// Importa la clase Request de Laravel para manejar solicitudes HTTP (GET, POST, etc.)
use Illuminate\Http\Request;

class PoliticaVacacionesController extends Controller
{
    /**
     * Muestra la pantalla de políticas de vacaciones
     */
    public function index()
    {
        // Obtener todas las políticas existentes
        $politicas = PoliticaVacaciones::all();

        // Enviar a la vista
        return view('politicas_vacaciones.index', compact('politicas'));
    }

    /**
     * Guarda una nueva política
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            // El tipo de contrato no puede repetirse
            'tipo_contrato' => 'required|string|max:50|unique:politicas_vacaciones,tipo_contrato',

            // Días anuales deben ser un número razonable
            'dias_anuales'  => 'required|integer|min:1|max:30',
        ]);

        // Crear la política
        PoliticaVacaciones::create([
            'tipo_contrato' => strtolower($request->tipo_contrato),
            'dias_anuales'  => $request->dias_anuales,
        ]);

        // Retornar con mensaje
        return back()->with('success', 'Política creada correctamente.');
    }

    /**
     * Actualiza los días anuales de una política existente
     */
    public function update(Request $request, $id)
    {
        // Validación
        $request->validate([
            'dias_anuales' => 'required|integer|min:1|max:30',
        ]);

        // Buscar la política
        $politica = PoliticaVacaciones::findOrFail($id);

        // Actualizar únicamente los días
        $politica->update([
            'dias_anuales' => $request->dias_anuales
        ]);

        return back()->with('success', 'Política actualizada correctamente.');
    }

 
        /**
    * Elimina una política de vacaciones
    * Se usa cuando el tipo de contrato fue escrito mal
    */
    public function destroy($id)
    {
      $politica = PoliticaVacaciones::findOrFail($id);
      $politica->delete();

      return back()->with('success', 'Política eliminada correctamente.');
    }

}
