<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente', function (Blueprint $table) {
            $table->id();
            $table->string('numero_radicacion')->unique();

            // Relaciones (Puestas como nullable por el momento para que no me de errores en caso de que aun no se hayan creado)
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->unsignedBigInteger('asesor_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();

            // Los campos del formulario
            $table->string('titulo');
            $table->string('tipo');

            // Campos de control del sistema
            $table->string('etapa')->nullable();
            $table->integer('fase_actual')->default(1);
            $table->string('estado')->default('activo');

            // Fechas de creación/actualización y borrado lógico
            $table->timestamps();
            $table->softDeletes(); //IMPORT 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente');
    }
};
