<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla INMUTABLE — sin updated_at ni deleted_at.
     * Cada verificación FAI genera una fila nueva; nunca se edita ni se borra.
     */
    public function up(): void
    {
        Schema::create('fairesultado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id');
            $table->string('fai_codigo', 10)->notNullable(); // RF02.1 … RF02.6

            $table->unsignedBigInteger('apifuente_id')->nullable();

            // aprobado | rechazado | pendiente | error | fallback_manual
            $table->string('estado', 20)->notNullable();

            $table->string('valor_obtenido', 200)->nullable(); // ej. "175 créditos"
            $table->string('valor_umbral', 200)->nullable();   // ej. "160 créditos mín."
            $table->jsonb('respuesta_raw')->nullable();

            $table->unsignedBigInteger('motivorechazo_id')->nullable();
            $table->unsignedBigInteger('validado_por_id')->nullable(); // persona que validó manualmente

            $table->string('ip_actor', 45)->nullable();
            $table->timestamp('verificado_at')->notNullable();
            $table->timestamp('created_at')->notNullable();    // sin updated_at ni deleted_at

            $table->foreign('expediente_id')->references('id')->on('expediente');
            $table->foreign('apifuente_id')->references('id')->on('apifuente');

            $table->index(['expediente_id', 'fai_codigo']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fairesultado');
    }
};
