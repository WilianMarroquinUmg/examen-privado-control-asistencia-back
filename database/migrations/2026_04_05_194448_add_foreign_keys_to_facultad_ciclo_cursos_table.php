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
        Schema::table('facultad_ciclo_cursos', function (Blueprint $table) {
            $table->foreign(['cursos_id'], 'fk_pensum_cursos_cursos1')->references(['id'])->on('cursos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['facultad_ciclo_id'], 'fk_pensum_cursos_pensum_ciclos1')->references(['id'])->on('facultad_ciclos')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facultad_ciclo_cursos', function (Blueprint $table) {
            $table->dropForeign('fk_pensum_cursos_cursos1');
            $table->dropForeign('fk_pensum_cursos_pensum_ciclos1');
        });
    }
};
