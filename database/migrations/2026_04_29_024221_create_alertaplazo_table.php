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
        Schema::create('alertaplazo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('controlplazo_id');
            $table->unsignedBigInteger('expediente_id');
            $table->unsignedBigInteger('destinatario_id')->nullable();
            $table->string('tipo_alerta');
            $table->string('canal')->default('interno');
            $table->dateTime('enviado_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertaplazo');
    }
};
