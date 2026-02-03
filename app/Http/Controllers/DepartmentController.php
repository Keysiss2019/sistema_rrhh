<?php

namespace App\Http\Controllers;

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
        $departamentos = Departamento::with('jefe')->get();

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
        // Busca el departamento o lanza error 404
        $departamento = Departamento::findOrFail($id);

        // Validación (ignora el nombre actual del departamento)
        $request->validate([
            'nombre' => 'required|unique:departamentos,nombre,' . $departamento->id,
            'descripcion' => 'required',
            'jefe_empleado_id' => 'nullable|exists:empleados,id'
        ]);

        // Actualiza el departamento
        $departamento->update($request->all());

        // Redirecciona con mensaje de éxito
        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento actualizado correctamente');
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
