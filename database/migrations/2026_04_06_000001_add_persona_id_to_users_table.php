<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('persona_id')->nullable()->constrained('persona')->after('id');
            $table->timestamp('ultimo_login_at')->nullable()->after('remember_token');
            $table->boolean('bloqueado')->default(false)->after('ultimo_login_at');
            $table->timestamp('bloqueado_hasta')->nullable()->after('bloqueado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn(['persona_id', 'ultimo_login_at', 'bloqueado', 'bloqueado_hasta']);
        });
    }
};
