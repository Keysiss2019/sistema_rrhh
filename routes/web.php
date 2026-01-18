<?php

/*
 Importación de controladores y facades necesarios
*/
use App\Http\Controllers\EmpleadoController;           // Controlador de empleados
use App\Http\Controllers\RoleController;               // Controlador de roles
use App\Http\Controllers\PermisosSistemaController;    // Controlador de permisos del sistema
use App\Http\Controllers\PermisoController;            // Controlador de permisos laborales
use App\Http\Controllers\PoliticaVacacionesController; // Controlador de políticas de vacaciones
use App\Http\Controllers\UsuarioController;            // Controlador de usuarios

use Illuminate\Support\Facades\Route;                  // Facade para definir rutas
use Illuminate\Support\Facades\Auth;                   // Facade para autenticación

/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL DEL SISTEMA
|--------------------------------------------------------------------------
| Ruta raíz que carga el dashboard o página inicial
| donde se muestra el menú principal del sistema.
*/
Route::get('/', function () {
    return view('index');
});

/*
|--------------------------------------------------------------------------
| RUTAS PARA GESTIÓN DE ROLES
|--------------------------------------------------------------------------
| Conjunto de rutas que permiten realizar el CRUD completo
| (crear, listar, editar y eliminar) de los roles del sistema.
*/
Route::get('/roles', [RoleController::class, 'index'])
    ->name('roles.index');     // Listado de roles

Route::get('/roles/create', [RoleController::class, 'create'])
    ->name('roles.create');    // Formulario de creación

Route::post('/roles', [RoleController::class, 'store'])
    ->name('roles.store');     // Guardar nuevo rol

Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
    ->name('roles.edit');      // Formulario de edición

Route::put('/roles/{id}', [RoleController::class, 'update'])
    ->name('roles.update');    // Actualizar rol

Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
    ->name('roles.destroy');   // Eliminar rol

/*
|--------------------------------------------------------------------------
| RUTAS PARA GESTIÓN DE EMPLEADOS
|--------------------------------------------------------------------------
| Permiten administrar los empleados del sistema
| mediante operaciones CRUD.
*/
Route::get('/empleado', [EmpleadoController::class, 'index'])
    ->name('empleado.index');   // Listado de empleados

Route::get('/empleado/create', [EmpleadoController::class, 'create'])
    ->name('empleado.create');  // Formulario de creación

Route::post('/empleado', [EmpleadoController::class, 'store'])
    ->name('empleado.store');   // Guardar empleado

Route::get('/empleado/{id}/edit', [EmpleadoController::class, 'edit'])
    ->name('empleado.edit');    // Formulario de edición

Route::put('/empleado/{id}', [EmpleadoController::class, 'update'])
    ->name('empleado.update');  // Actualizar empleado

Route::delete('/empleado/{id}', [EmpleadoController::class, 'destroy'])
    ->name('empleado.destroy'); // Eliminar empleado

/*
|--------------------------------------------------------------------------
| RUTAS PARA GESTIÓN DE USUARIOS
|--------------------------------------------------------------------------
| Administración de usuarios del sistema (crear, listar,
| eliminar y cambiar estado activo/inactivo).
*/
Route::get('/usuarios', [UsuarioController::class, 'index'])
    ->name('usuarios.index');   // Listado de usuarios

Route::get('/usuarios/create', [UsuarioController::class, 'create'])
    ->name('usuarios.create');  // Formulario de creación

Route::post('/usuarios', [UsuarioController::class, 'store'])
    ->name('usuarios.store');   // Guardar usuario

Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])
    ->name('usuarios.destroy'); // Eliminar usuario

Route::put('/usuarios/{id}/estado', [UsuarioController::class, 'toggleEstado'])
    ->name('usuarios.estado');  // Activar / desactivar usuario

/*
|--------------------------------------------------------------------------
| RUTAS PARA GESTIÓN DE PERMISOS DEL SISTEMA
|--------------------------------------------------------------------------
| Permiten asignar y actualizar los permisos del sistema
| asociados a roles o usuarios.
*/
Route::get(
    '/seguridad/permisos',
    [PermisosSistemaController::class, 'index']
)->name('permisos_sistema.index');   // Pantalla de permisos del sistema

Route::post(
    '/seguridad/permisos',
    [PermisosSistemaController::class, 'update']
)->name('permisos_sistema.update');  // Actualización de permisos

/*
 RUTAS PARA GESTIÓN DE PERMISOS LABORALES
*/

// Ejemplo de agrupación con middleware (comentado por ahora)
// Route::middleware(['auth'])->group(function () {
//     Route::resource('permisos', PermisoController::class);
// });

// Mostrar formulario de solicitud de permiso
Route::get('/permisos/create', [PermisoController::class, 'create'])
    ->name('permisos.create');

// Guardar solicitud de permiso
Route::post('/permisos', [PermisoController::class, 'store'])
    ->name('permisos.store');

// Listado de permisos solicitados
Route::get('/permisos', [PermisoController::class, 'index'])
    ->name('permisos.index');

/*
|--------------------------------------------------------------------------
| RUTAS PARA POLÍTICAS DE VACACIONES
|--------------------------------------------------------------------------
| Estas rutas permiten gestionar las políticas de vacaciones
| del sistema (días anuales, reglas, etc.).
| Deben ser accesibles solo por Administrador o RRHH.
*/

// Route::middleware(['auth'])->group(function () {

    // Mostrar listado de políticas
    Route::get(
        '/politicas-vacaciones',
        [PoliticaVacacionesController::class, 'index']
    )->name('politicas.index');

    // Crear una nueva política
    Route::post(
        '/politicas-vacaciones',
        [PoliticaVacacionesController::class, 'store']
    )->name('politicas.store');

    // Actualizar política existente
    Route::put(
        '/politicas-vacaciones/{id}',
        [PoliticaVacacionesController::class, 'update']
    )->name('politicas.update');

    // Eliminar política
    Route::delete(
        '/politicas-vacaciones/{id}',
        [PoliticaVacacionesController::class, 'destroy']
    )->name('politicas.destroy');

// });  ← Descomentar cuando se habilite autenticación

/*
 RUTA PARA CERRAR SESIÓN
*/
Route::post('/logout', function () {
    Auth::logout();      // Cierra la sesión activa
    return redirect('/'); // Redirige al inicio
})->name('logout');

