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
        Schema::create('det_expedientealerta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expediente');
            $table->foreignId('plazo_id')->constrained('det_expedienteplazo');
            $table->string('tipo')->default('vencimiento_15_dias');
            $table->text('mensaje');
            $table->boolean('enviado_comite')->default(false);
            $table->dateTime('fecha_alerta')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('det_expedientealerta');
    }
};
