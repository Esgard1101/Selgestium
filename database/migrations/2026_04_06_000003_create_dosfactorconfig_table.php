<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosfactorconfig', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->enum('driver', ['totp', 'sms'])->default('totp');
            $table->string('secret_totp')->nullable(); // cifrado en capa de aplicación
            $table->string('telefono_sms', 20)->nullable();
            $table->boolean('activo')->default(false);
            $table->timestamp('activado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosfactorconfig');
    }
};
