<?php

// Indica que esta clase pertenece al espacio de nombres App\Http\Controllers
namespace App\Http\Controllers;

// Importa el modelo PoliticaVacaciones para poder interactuar con la tabla correspondiente
use App\Models\PoliticaVacaciones;

// Importa el modelo Empleado para poder interactuar con la tabla correspondiente
use App\Models\Empleado;

// Importa la clase Request de Laravel para manejar solicitudes HTTP (GET, POST, etc.)
use Illuminate\Http\Request;

class PoliticaVacacionesController extends Controller
{
    public function index()
    {
        // Ordenamos por tipo y año para que la tabla se vea organizada
        $politicas = PoliticaVacaciones::orderBy('tipo_contrato')
                                        ->orderBy('anio_antiguedad')
                                        ->get();

        return view('politicas_vacaciones.index', compact('politicas'));
    }

    public function store(Request $request)
    {
        // 1. Si es Permanente, procesamos la escala de Honduras
        if ($request->tipo_contrato === 'permanente') {
            
            // Validamos que vengan los días de la escala
            $request->validate([
                'dias_permanente' => 'required|array|size:4',
                'dias_permanente.*' => 'required|integer|min:1|max:30',
            ]);

            foreach ($request->dias_permanente as $anio => $dias) {
                // Usamos updateOrCreate para que si ya existen los años 1,2,3,4, solo los actualice
                PoliticaVacaciones::updateOrCreate(
                    [
                        'tipo_contrato' => 'permanente',
                        'anio_antiguedad' => $anio
                    ],
                    ['dias_anuales' => $dias]
                );
            }

            return back()->with('success', 'Escala de vacaciones permanente configurada correctamente.');
        } 

        // 2. Si es otro tipo (Temporal, etc.), validamos y guardamos normal
        $request->validate([
            // Validamos que no exista ya ese contrato para ese año 1
            'tipo_contrato' => 'required|string|max:50',
            'dias_fijos' => 'required|integer|min:1|max:30',
        ]);

        // Verificamos si ya existe para evitar duplicados en tipos simples
        $existe = PoliticaVacaciones::where('tipo_contrato', strtolower($request->tipo_contrato))
                                    ->where('anio_antiguedad', 1)
                                    ->exists();
        
        if ($existe) {
            return back()->with('error', 'Esta política ya existe. Si desea cambiarla, use el botón de actualizar en la tabla.');
        }

        PoliticaVacaciones::create([
            'tipo_contrato' => strtolower($request->tipo_contrato),
            'anio_antiguedad' => 1,
            'dias_anuales' => $request->dias_fijos,
        ]);

        return back()->with('success', 'Política creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dias_anuales' => 'required|integer|min:1|max:30',
        ]);

        $politica = PoliticaVacaciones::findOrFail($id);
        $politica->update([
            'dias_anuales' => $request->dias_anuales
        ]);

        return back()->with('success', 'Días actualizados correctamente.');
    }

    public function destroy($id)
    {
        $politica = PoliticaVacaciones::findOrFail($id);

        // Verificamos si hay empleados con este contrato
        $empleadosUsando = Empleado::where('tipo_contrato', $politica->tipo_contrato)->count();

        if ($empleadosUsando > 0) {
            return back()->with('error', "No se puede eliminar. Hay $empleadosUsando empleado(s) vinculados a este tipo de contrato.");
        }

        $politica->delete();
        return back()->with('success', 'Política eliminada correctamente.');
    }
}
