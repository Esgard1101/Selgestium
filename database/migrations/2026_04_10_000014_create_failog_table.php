<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log HTTP detallado para llamadas a APIs externas.
     * Solo se usa cuando modo_operacion = 'api'.
     * Tabla inmutable — sin updated_at ni deleted_at.
     */
    public function up(): void
    {
        Schema::create('failog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id');
            $table->unsignedBigInteger('apifuente_id')->nullable();
            $table->string('fai_codigo', 10)->notNullable();
            $table->string('url_llamada', 500)->nullable();
            $table->string('metodo_http', 10)->default('POST');
            $table->jsonb('request_payload')->nullable();  // sin tokens
            $table->jsonb('response_body')->nullable();
            $table->smallInteger('http_status')->nullable();
            $table->integer('duracion_ms')->nullable();
            $table->timestamp('created_at')->notNullable();

            $table->foreign('expediente_id')->references('id')->on('expediente');
            $table->foreign('apifuente_id')->references('id')->on('apifuente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failog');
    }
};
