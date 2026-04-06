<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('det_expedientedocumento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id'); // Para enlazarlo con el expediente que acabas de crear
            $table->string('ruta_archivo'); // Aquí se guarda la ruta de tu PDF
            $table->string('tipo_documento'); // Para saber que es un "proyecto"

            $table->timestamps();
            $table->softDeletes(); // Por si acaso tu modelo también lo pide
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('det_expedientedocumento');
    }
};
