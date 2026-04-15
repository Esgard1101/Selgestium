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
            // Claves foráneas reales conectadas a las tablas de Esgardo
            $table->foreignId('expediente_id')->constrained('expediente');

            $table->string('tipo_documento');
            $table->string('nombre_original');
            $table->string('ruta_almacenamiento');
            $table->integer('tamanio_bytes');
            $table->string('hash_sha256', 64);

            // Relación con la tabla persona (súper importante para la auditoría)
            $table->foreignId('subido_por_id')->constrained('persona');

            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('det_expedientedocumento');
    }
};
