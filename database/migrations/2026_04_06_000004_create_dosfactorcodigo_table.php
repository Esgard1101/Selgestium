<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla inmutable: sin updated_at, sin softDeletes
        Schema::create('dosfactorcodigo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('codigo', 10);
            $table->enum('proposito', ['firma_jurado', 'aprobacion_ui', 'emision_resolucion']);
            $table->foreignId('expediente_id')->nullable()->constrained('expediente')->nullOnDelete();
            $table->timestamp('expira_at');
            $table->timestamp('usado_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosfactorcodigo');
    }
};
