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
        Schema::table('trabajo_espacios', function (Blueprint $table) {
            $table->foreign(['ciclo_id'], 'fk_trabajo_espacios_ciclos1')->references(['id'])->on('ciclos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['curso_id'], 'fk_trabajo_espacios_cursos1')->references(['id'])->on('cursos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['facultad_id'], 'fk_trabajo_espacios_facultades1')->references(['id'])->on('facultades')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['catedratico_id'], 'fk_trabajo_espacios_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trabajo_espacios', function (Blueprint $table) {
            $table->dropForeign('fk_trabajo_espacios_ciclos1');
            $table->dropForeign('fk_trabajo_espacios_cursos1');
            $table->dropForeign('fk_trabajo_espacios_facultades1');
            $table->dropForeign('fk_trabajo_espacios_users1');
        });
    }
};
