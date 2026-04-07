<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rolpersona', function (Blueprint $table) {
            // Agregar persona_id como FK hacia la tabla persona
            $table->foreignId('persona_id')->nullable()->constrained('persona')->after('id');
            // Índice único: una persona, un rol, por sucursal
            $table->unique(['persona_id', 'rol_id', 'sucursal_id'], 'rolpersona_persona_rol_sucursal_unique');
        });

        // Migrar datos existentes: copiar usuario_id → persona_id via users.persona_id
        DB::statement('
            UPDATE rolpersona rp
            SET persona_id = u.persona_id
            FROM users u
            WHERE rp.usuario_id = u.id
              AND u.persona_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('rolpersona', function (Blueprint $table) {
            $table->dropUnique('rolpersona_persona_rol_sucursal_unique');
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
    }
};
