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
        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->foreign(['toma_asistencia_id'], 'fk_asistencia_registros_asistencia_sesion_tomas1')->references(['id'])->on('asistencia_sesion_tomas')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['alumno_id'], 'fk_asistencia_registros_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_registros', function (Blueprint $table) {
            $table->dropForeign('fk_asistencia_registros_asistencia_sesion_tomas1');
            $table->dropForeign('fk_asistencia_registros_users1');
        });
    }
};
