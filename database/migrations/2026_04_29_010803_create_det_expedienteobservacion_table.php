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
            $table->bigInteger('expediente_id')->unsigned();
            $table->bigInteger('jurado_id')->unsigned();
            $table->bigInteger('tipoobservacion_id')->unsigned()->nullable();
            $table->integer('ronda')->default(1);
            $table->text('descripcion');
            $table->boolean('bloqueado')->default(true);
            $table->boolean('subsanado')->default(false);
            $table->timestamp('fecha_subsanacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('expediente_id')->references('id')->on('expediente')->onDelete('cascade');
            $table->foreign('jurado_id')->references('id')->on('persona')->onDelete('cascade');
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
