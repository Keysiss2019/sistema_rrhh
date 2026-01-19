<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos el campo después de la contraseña
            // Por defecto es 0 (no se le exige cambio), el admin lo activa al crear
            $table->boolean('debe_cambiar_password')->default(0)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si hacemos rollback, eliminamos la columna
            $table->dropColumn('debe_cambiar_password');
        });
    }
};