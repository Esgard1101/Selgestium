<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expediente')) {
            return;
        }

        DB::table('expediente')->whereNull('tipo')->update(['tipo' => 'cuantitativo']);
        DB::table('expediente')->whereNull('etapa')->update(['etapa' => 'I']);
        DB::table('expediente')->whereNull('estado')->update(['estado' => 'pendiente']);

        DB::statement("ALTER TABLE expediente ALTER COLUMN tipo SET DEFAULT 'cuantitativo'");
        DB::statement("ALTER TABLE expediente ALTER COLUMN etapa SET DEFAULT 'I'");
        DB::statement("ALTER TABLE expediente ALTER COLUMN fase_actual SET DEFAULT 1");
        DB::statement("ALTER TABLE expediente ALTER COLUMN estado SET DEFAULT 'pendiente'");

        Schema::table('expediente', function (Blueprint $table) {
            $table->foreign('estudiante_id')->references('id')->on('persona');
            $table->foreign('asesor_id')->references('id')->on('persona');
            $table->foreign('sucursal_id')->references('id')->on('sucursal');
            $table->index(['sucursal_id', 'estado', 'fase_actual'], 'expediente_estado_fase_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('expediente')) {
            return;
        }

        Schema::table('expediente', function (Blueprint $table) {
            $table->dropIndex('expediente_estado_fase_idx');
            $table->dropForeign(['estudiante_id']);
            $table->dropForeign(['asesor_id']);
            $table->dropForeign(['sucursal_id']);
        });
    }
};
