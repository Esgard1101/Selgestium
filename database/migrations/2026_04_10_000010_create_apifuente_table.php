<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apifuente', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();          // DSA, DGA, RENIEC, SUNEDU, TURNITIN, ALICIA
            $table->string('descripcion', 100)->notNullable();
            $table->string('tipo_protocolo', 10)->default('REST'); // REST | SOAP
            $table->smallInteger('timeout_segundos')->default(30);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Seeds fijos — nunca cambian
        DB::table('apifuente')->insert([
            ['codigo' => 'DSA',      'descripcion' => 'Sistema Académico UNPRG (Créditos)',      'tipo_protocolo' => 'REST', 'timeout_segundos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'DGA',      'descripcion' => 'Tesorería UNPRG (Voucher de pago)',        'tipo_protocolo' => 'REST', 'timeout_segundos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'RENIEC',   'descripcion' => 'RENIEC — Identidad del usuario (DNI)',     'tipo_protocolo' => 'REST', 'timeout_segundos' => 15, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'SUNEDU',   'descripcion' => 'SUNEDU / ORC — Grado de Bachiller',       'tipo_protocolo' => 'REST', 'timeout_segundos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'TURNITIN', 'descripcion' => 'Turnitin — Similitud antiplagiarismo',    'tipo_protocolo' => 'REST', 'timeout_segundos' => 60, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'ALICIA',   'descripcion' => 'ALICIA CONCYTEC — Duplicidad de tesis',   'tipo_protocolo' => 'REST', 'timeout_segundos' => 30, 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('apifuente');
    }
};
