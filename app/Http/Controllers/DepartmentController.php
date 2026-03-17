<?php

namespace App\Http\Controllers; // Define el namespace del modelo dentro de la aplicación (organización lógica del código)

// Modelo de Departamentos
use App\Models\Departamento;

// Modelo de Empleados
use App\Models\Empleado;

// Request para validaciones y datos del formulario
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Muestra la lista de departamentos
     * junto con su jefe asignado y empleados activos
     */
    public function index()
    {
        // Obtiene todos los departamentos con su relación jefe
        $departamentos = Departamento::with('jefeEmpleado')->get();

        // Obtiene solo empleados activos (para selects)
        $empleados = Empleado::where('estado', 'activo')->get();

        // Retorna la vista principal de departamentos
        return view('departamentos.index', compact('departamentos', 'empleados'));
    }

    /**
     * Guarda un nuevo departamento
     */
    public function store(Request $request)
    {
        // Validación de datos de entrada
        $request->validate([
         'nombre' => 'required|unique:departamentos,nombre,' . ($id ?? 'NULL') . ',id',
         'descripcion' => 'required',
          'jefe_empleado_id' => 'nullable|exists:empleados,id'
        ], [
          'nombre.required' => 'El nombre del departamento es obligatorio.',
          'nombre.unique' => 'Ya existe un departamento con ese nombre.',
          'descripcion.required' => 'La descripción es obligatoria.',
        ]);


        // Crea el departamento con los datos validados
        Departamento::create($request->all());

        // Redirecciona con mensaje de éxito
        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento creado correctamente');
    }

    /**
     * Actualiza un departamento existente
     */
  public function update(Request $request, $id)
    {
      $departamento = Departamento::findOrFail($id);

       $request->validate([
          'nombre' => 'required|unique:departamentos,nombre,' . $departamento->id,
          'descripcion' => 'required',
          'jefe_empleado_id' => 'nullable|exists:empleados,id'
        ]);

       // VALIDACIÓN: ¿El empleado ya es jefe de otro departamento?
       if ($request->jefe_empleado_id) {
          $otroDepto = Departamento::where('jefe_empleado_id', $request->jefe_empleado_id)
                                 ->where('id', '!=', $id) // Que no sea este mismo
                                 ->first();
        
          if ($otroDepto) {
              return redirect()->back()
             ->with('warning', "Este empleado ya es jefe del departamento: " . $otroDepto->nombre)
             ->with('edit_url', route('departamentos.update', $id))
             // Pasamos el ID del jefe que ya tenía el departamento
             ->with('id_jefe_original', $departamento->jefe_empleado_id) 
             ->withInput(); 
            }
        }

        $departamento->update($request->all());

       return redirect()->route('departamentos.index')
        ->with('success', "¡Departamento actualizado con éxito!");
    }

    /**
     * Elimina un departamento
     */
    public function destroy($id)
    {
        // Busca y elimina el departamento
        Departamento::findOrFail($id)->delete();

        // Redirecciona con mensaje de éxito
        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento eliminado correctamente');
    }
}
