<?php

namespace App\Http\Controllers;

use App\Models\Role;      // Modelo de roles
use Illuminate\Http\Request; // Manejo de peticiones HTTP

class RoleController extends Controller
{
    /**
     * Mostrar listado de todos los roles
     */
    public function index()
    {
        // Obtener todos los roles de la BD
        $roles = Role::all();

        // Enviar roles a la vista roles.index
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear un nuevo rol
     */
    public function create()
    {
        return view('roles.create'); // Vista con formulario
    }

    /**
     * Guardar un nuevo rol en la base de datos
     */
    public function store(Request $request)
    {
        // Validar que el nombre sea obligatorio y único
        $request->validate([
            'nombre' => 'required|unique:roles,nombre',
            'descripcion' => 'required',
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Este rol ya existe.',
            'descripcion.required' => 'La descripción es obligatoria.'
        ]);

        // Crear el rol usando Mass Assignment
        Role::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        // Redireccionar al listado con mensaje de éxito
        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente');
    }
}
