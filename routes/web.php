<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;

// Rutas para CRUD de Roles
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');

Route::get('/', function () {
    return view('welcome');
});
