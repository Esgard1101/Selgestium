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
        Schema::create('expediente', function (Blueprint $table) {
            $table->id();
            $table->string('numero_radicacion')->unique();
            $table->foreignId('estudiante_id')->constrained('persona');
            $table->foreignId('asesor_id')->nullable()->constrained('persona');
            $table->foreignId('sucursal_id')->constrained('sucursal');
            $table->string('titulo');
            $table->string('tipo')->default('cuantitativo'); // fk tipoinvestigacion
            $table->string('etapa')->default('I'); // I = Proyecto, II = Informe
            $table->unsignedTinyInteger('fase_actual')->default(1); // 1-11
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['sucursal_id', 'estado', 'fase_actual']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expediente');
    }
};
