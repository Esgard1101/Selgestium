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
        Schema::create('bit_expediente', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expediente_id')->unsigned();
            $table->bigInteger('actor_id')->unsigned()->nullable();
            $table->string('accion');
            $table->string('ip')->nullable();
            $table->timestamps();

            $table->foreign('expediente_id')->references('id')->on('expediente')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bit_expediente');
    }
};
