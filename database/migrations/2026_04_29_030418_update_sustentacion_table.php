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
        Schema::table('sustentacion', function (Blueprint $table) {
            if (Schema::hasColumn('sustentacion', 'fecha')) {
                $table->dropColumn(['fecha', 'hora']);
            }
            $table->dateTime('fecha_hora')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->string('modalidad', 20)->default('presencial');
            $table->string('enlace_virtual', 255)->nullable();
            $table->unsignedBigInteger('resolucion_id')->nullable();
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->string('resultado', 50)->nullable();
            $table->string('acta_url', 255)->nullable();
            
            // Unique constraint on expediente_id
            $table->unique('expediente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sustentacion', function (Blueprint $table) {
            $table->dropUnique(['expediente_id']);
            $table->dropColumn([
                'fecha_hora', 'sucursal_id', 'modalidad', 
                'enlace_virtual', 'resolucion_id', 'nota_final', 
                'resultado', 'acta_url'
            ]);
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
        });
    }
};
