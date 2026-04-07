<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla inmutable de auditoría: sin updated_at, sin softDeletes
        Schema::create('bit_accesousuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('accion', ['login', 'logout', 'login_fallido', 'sesion_expirada']);
            $table->string('ip', 45);
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursal')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bit_accesousuario');
    }
};
