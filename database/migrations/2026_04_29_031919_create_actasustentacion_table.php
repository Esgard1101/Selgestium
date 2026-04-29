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
        Schema::create('actasustentacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sustentacion_id')->unique()->constrained('sustentacion');
            $table->foreignId('expediente_id')->constrained('expediente');
            $table->decimal('nota_jurado1', 4, 2);
            $table->decimal('nota_jurado2', 4, 2);
            $table->decimal('nota_jurado3', 4, 2);
            $table->decimal('nota_promedio', 4, 2);
            $table->string('resultado', 50);
            $table->text('observaciones_acta')->nullable();
            $table->string('acta_url', 255)->nullable();
            $table->timestamp('firmado_por_presidente_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actasustentacion');
    }
};
