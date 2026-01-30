<?php

/*
 Importación de controladores y facades necesarios
*/
use App\Http\Controllers\EmpleadoController;           // Controlador de empleados
use App\Http\Controllers\RoleController;               // Controlador de roles
use App\Http\Controllers\PermisosSistemaController;    // Controlador de permisos del sistema
use App\Http\Controllers\SolicitudController;            // Controlador de permisos laborales
use App\Http\Controllers\PoliticaVacacionesController; // Controlador de políticas de vacaciones
use App\Http\Controllers\UsuarioController;            // Controlador de usuarios
use App\Http\Controllers\LoginController;              //controla el inicio de sesión
use Illuminate\Support\Facades\Route;                  // Facade para definir rutas
use Illuminate\Support\Facades\Auth;                   // Facade para autenticación
use App\Http\Controllers\TiempoCompensatorioController; // Controlador de tiempo compensatorio
use App\Http\Controllers\HoraExtraController;           // Controlador de horas extras




/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (GUEST)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Raíz redirige a Login
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'force.password.change'])->group(function () {

    // --- DASHBOARD / INICIO ---
   Route::get('/dashboard', fn() => view('index'))->name('dashboard');

    // --- SEGURIDAD Y PERFIL ---
    Route::get('/cambiar-password', fn() => view('auth.cambiar-password'))->name('password.cambiar');
    Route::post('/actualizar-password', [UsuarioController::class, 'actualizarPassword'])->name('password.actualizar');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // --- MÓDULO: GESTIÓN DE USUARIOS ---
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::put('/usuarios/{id}/estado', [UsuarioController::class, 'toggleEstado'])->name('usuarios.estado');

    // --- MÓDULO: ROLES Y PERMISOS ---
    Route::resource('roles', RoleController::class)->except(['show']);

    // --- MÓDULO: EXPEDIENTE DE EMPLEADOS ---
    Route::resource('empleado', EmpleadoController::class);

    // --- MÓDULO: SEGURIDAD DE MÓDULOS ---
    Route::get('/seguridad/permisos', [PermisosSistemaController::class, 'index'])->name('permisos_sistema.index');
    Route::post('/seguridad/permisos', [PermisosSistemaController::class, 'update'])->name('permisos_sistema.update');
    
    // --- MÓDULO: PERMISOS LABORALES ---
    // --- MÓDULO  DE SOLICITUDES ---
    Route::prefix('solicitudes')->group(function () {
      // Listado de solicitudes
      Route::get('/', [SolicitudController::class, 'index'])->name('solicitudes.index');
    
      // Ver detalle / Formato de impresión/ Editar
      Route::get('/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
      Route::put('/solicitudes/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');
     
      // Procesar aprobación o rechazo
      Route::post('/{id}/procesar', [SolicitudController::class, 'procesar'])->name('solicitudes.procesar');
      Route::patch('/{id}/estado', [SolicitudController::class, 'procesar'])->name('solicitudes.procesar');
      Route::post('/solicitudes/{id}/rectificar', [SolicitudController::class, 'rectificarTipo'])->name('solicitudes.rectificar');
  

    });


 // --- RUTAS PARA PERMISOS (USO DE TIEMPO) ---
Route::prefix('tiempo-compensatorio')->group(function () {
    Route::get('/', [TiempoCompensatorioController::class, 'index'])->name('tiempo_compensatorio.index');
    Route::get('/crear', [TiempoCompensatorioController::class, 'create'])->name('tiempo_compensatorio.create');
    Route::post('/store', [TiempoCompensatorioController::class, 'store'])->name('tiempo_compensatorio.store');
    Route::get('/{id}', [TiempoCompensatorioController::class, 'show'])->name('tiempo_compensatorio.show');
    Route::delete('/{id}', [TiempoCompensatorioController::class, 'destroy'])->name('tiempo_compensatorio.destroy');
    
});

// --- RUTAS PARA HORAS EXTRAS (REGISTRO FT-GTH-002) ---
Route::prefix('horas-extras')->group(function () {
    // Para guardar desde el modal
    Route::post('/store', [HoraExtraController::class, 'store'])->name('horas_extras.store');
    
    // Para ver la lista de pendientes (la que vería el jefe)
    Route::get('/pendientes', [HoraExtraController::class, 'pendientes'])->name('horas_extras.pendientes');
    
    // Para aprobar o rechazar (usamos validar para que coincida con el controlador anterior)
    Route::patch('/{id}/validar', [HoraExtraController::class, 'validar'])->name('horas_extras.validar');
});
    // --- MÓDULO: POLÍTICAS DE VACACIONES ---
    Route::get('/politicas-vacaciones', [PoliticaVacacionesController::class, 'index'])->name('politicas.index');
    Route::post('/politicas-vacaciones', [PoliticaVacacionesController::class, 'store'])->name('politicas.store');
    Route::put('/politicas-vacaciones/{id}', [PoliticaVacacionesController::class, 'update'])->name('politicas.update');
    Route::delete('/politicas-vacaciones/{id}', [PoliticaVacacionesController::class, 'destroy'])->name('politicas.destroy');

    // Redirección si ya está logueado
    Route::get('/', function () {
       return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
    });
});

 