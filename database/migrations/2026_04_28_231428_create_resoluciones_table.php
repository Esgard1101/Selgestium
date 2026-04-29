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
        Schema::create('resoluciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->unsignedBigInteger('tiporesolucion_id')->nullable();
            $table->string('numero_resolucion');
            $table->date('fecha_emision')->nullable();
            $table->string('documento_url')->nullable();
            $table->unsignedBigInteger('emitido_por_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('expediente_id')->references('id')->on('expediente')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursal')->onDelete('set null');
            $table->foreign('emitido_por_id')->references('id')->on('persona')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resoluciones');
    }
};
