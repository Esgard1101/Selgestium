<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('det_expedientefase', function (Blueprint $table) {
            if (!Schema::hasColumn('det_expedientefase', 'fecha_inicio')) {
                $table->timestamp('fecha_inicio')->nullable()->after('ip_actor');
            }
            if (!Schema::hasColumn('det_expedientefase', 'fecha_fin')) {
                $table->timestamp('fecha_fin')->nullable()->after('fecha_inicio');
            }
        });
    }

    public function down()
    {
        Schema::table('det_expedientefase', function (Blueprint $table) {
            $table->dropColumn(['fecha_inicio', 'fecha_fin']);
        });
    }
};
