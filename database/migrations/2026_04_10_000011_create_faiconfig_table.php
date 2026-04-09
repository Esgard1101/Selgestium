<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faiconfig', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('apifuente_id')->nullable(); // nullable en modo manual
            $table->string('fai_codigo', 10)->notNullable();        // RF02.1 … RF02.6
            $table->string('url_servicio', 500)->nullable();
            $table->text('token_servicio')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('modo_operacion', 20)->default('manual'); // manual | api
            $table->timestamps();

            $table->foreign('sucursal_id')->references('id')->on('sucursal');
            $table->foreign('apifuente_id')->references('id')->on('apifuente');

            $table->unique(['sucursal_id', 'fai_codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faiconfig');
    }
};
