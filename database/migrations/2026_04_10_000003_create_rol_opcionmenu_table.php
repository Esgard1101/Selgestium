<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_opcionmenu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('rol')->cascadeOnDelete();
            $table->foreignId('opcionmenu_id')->constrained('opcionmenu')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['rol_id', 'opcionmenu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_opcionmenu');
    }
};
