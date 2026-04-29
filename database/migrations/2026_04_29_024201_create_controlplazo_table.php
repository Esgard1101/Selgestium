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
        Schema::create('controlplazo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id');
            $table->integer('fase_id');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_vencimiento');
            $table->integer('dias_habiles')->default(15);
            $table->boolean('vencido')->default(false);
            $table->boolean('art123d_habilitado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controlplazo');
    }
};
