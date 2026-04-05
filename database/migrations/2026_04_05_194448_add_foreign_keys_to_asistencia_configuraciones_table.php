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
        Schema::table('asistencia_configuraciones', function (Blueprint $table) {
            $table->foreign(['facultad_ciclo_curso_id'], 'fk_asistencia_configuraciones_facultad_ciclo_cursos1')->references(['id'])->on('facultad_ciclo_cursos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['catedratico_id'], 'fk_asistencia_configuraciones_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_configuraciones', function (Blueprint $table) {
            $table->dropForeign('fk_asistencia_configuraciones_facultad_ciclo_cursos1');
            $table->dropForeign('fk_asistencia_configuraciones_users1');
        });
    }
};
