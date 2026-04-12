<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public
    function up(): void
    {
        Schema::table('asistencia_configuraciones', function (Blueprint $table) {

            $table->foreign(['catedratico_id'], 'fk_asistencia_configuraciones_users1')
                ->references(['id'])
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');


            $table->foreign(['facultad_id'], 'fk_configuraciones_facultad_id')
                ->references(['id'])
                ->on('facultades')
                ->onUpdate('no action')
                ->onDelete('no action');

            $table->foreign(['ciclo_id'], 'fk_configuraciones_ciclo_id')
                ->references(['id'])
                ->on('facultades')
                ->onUpdate('no action')
                ->onDelete('no action');

            $table->foreign(['curso_id'], 'fk_configuraciones_curso_id')
                ->references(['id'])
                ->on('cursos')
                ->onUpdate('no action')
                ->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_configuraciones', function (Blueprint $table) {
            $table->dropForeign('fk_asistencia_configuraciones_users1');
            $table->dropForeign('fk_configuraciones_facultad_id');
            $table->dropForeign('fk_configuraciones_ciclo_id');
            $table->dropForeign('fk_configuraciones_curso_id');
        });
    }
};
