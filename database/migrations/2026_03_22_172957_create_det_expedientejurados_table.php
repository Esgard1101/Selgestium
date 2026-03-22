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
        Schema::create('det_expedientejurado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expediente');
            $table->foreignId('jurado_id')->constrained('persona');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('det_expedientejurado');
    }
};
