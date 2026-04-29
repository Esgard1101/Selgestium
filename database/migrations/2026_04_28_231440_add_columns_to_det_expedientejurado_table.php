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
        Schema::table('det_expedientejurado', function (Blueprint $table) {
            $table->string('rol_jurado')->nullable()->after('jurado_id');
            $table->timestamp('fecha_asignacion')->nullable()->after('rol_jurado');
            $table->unsignedBigInteger('resolucion_id')->nullable()->after('fecha_asignacion');
            $table->boolean('aprobado')->nullable()->after('resolucion_id'); // NULL=pendiente
            $table->timestamp('fecha_evaluacion')->nullable()->after('aprobado');
            $table->string('codigo_2fa_usado')->nullable()->after('fecha_evaluacion');
            $table->boolean('activo')->default(true)->after('codigo_2fa_usado');

            // Unique constraint
            $table->unique(['expediente_id', 'jurado_id', 'rol_jurado'], 'det_expedientejurado_unique');

            // Foreign key for resolucion
            $table->foreign('resolucion_id')->references('id')->on('resoluciones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('det_expedientejurado', function (Blueprint $table) {
            $table->dropForeign(['resolucion_id']);
            $table->dropUnique('det_expedientejurado_unique');
            $table->dropColumn([
                'rol_jurado',
                'fecha_asignacion',
                'resolucion_id',
                'aprobado',
                'fecha_evaluacion',
                'codigo_2fa_usado',
                'activo'
            ]);
        });
    }
};
