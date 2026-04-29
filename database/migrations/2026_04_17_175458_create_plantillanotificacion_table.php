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
        Schema::create('plantillanotificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable(); // NULL significa que aplica para todas las sucursales
            $table->string('codigo')->unique(); // Ej: 'EXP_RADICADO'
            $table->string('canal'); // Ej: 'sistema', 'email'
            $table->string('asunto');
            $table->text('cuerpo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantillanotificacion');
    }
};
