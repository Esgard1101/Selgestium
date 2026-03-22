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
        Schema::create('det_expedienteobservacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expediente');
            $table->foreignId('jurado_id')->constrained('persona');
            $table->unsignedTinyInteger('ronda')->default(1);
            $table->text('descripcion');
            $table->boolean('bloqueado')->default(false);
            $table->string('tipo_veredicto')->default('observado'); // aprobado | observado
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('det_expedienteobservacion');
    }
};
