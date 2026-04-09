<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reintentos cuando la API externa falla.
     * Tabla inmutable — sin updated_at ni deleted_at.
     */
    public function up(): void
    {
        Schema::create('faiintento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id');
            $table->string('fai_codigo', 10)->notNullable();
            $table->smallInteger('intento_numero')->notNullable(); // 1, 2, 3…
            $table->smallInteger('http_status')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->integer('duracion_ms')->nullable();
            $table->timestamp('created_at')->notNullable();

            $table->foreign('expediente_id')->references('id')->on('expediente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faiintento');
    }
};
