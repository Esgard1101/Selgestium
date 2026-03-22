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
        Schema::create('det_expedienteacta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expediente');
            $table->string('numero_acta')->unique();
            $table->dateTime('fecha_sustentacion');
            $table->string('resultado'); // Aprobado, Desaprobado, Mención Honrosa
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('det_expedienteactas');
    }
};
