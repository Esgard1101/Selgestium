<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('det_expedientefase', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id'); // El expediente afectado
            $table->integer('fase_id'); // La fase en la que está 
            $table->unsignedBigInteger('actor_id'); // Quién hizo el cambio
            $table->text('comentario')->nullable(); // "Radicación inicial del expediente"

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('det_expedientefase');
    }
};
