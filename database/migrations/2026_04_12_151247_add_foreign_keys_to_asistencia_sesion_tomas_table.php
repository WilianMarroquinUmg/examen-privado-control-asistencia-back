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
        Schema::table('asistencia_sesion_tomas', function (Blueprint $table) {
            $table->foreign(['sesion_id'], 'fk_asistencia_sesion_tomas_asistencia_sessiones1')->references(['id'])->on('asistencia_sesiones')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_sesion_tomas', function (Blueprint $table) {
            $table->dropForeign('fk_asistencia_sesion_tomas_asistencia_sessiones1');
        });
    }
};
