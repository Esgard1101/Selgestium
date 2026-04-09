<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opcionmenu', function (Blueprint $table) {
            $table->id();
            $table->string('ruta_nombre', 100)->unique()->nullable();
            $table->string('descripcion', 150);
            $table->string('icono', 100)->default('fas fa-circle');
            $table->string('grupo', 80)->nullable();
            $table->smallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opcionmenu');
    }
};
