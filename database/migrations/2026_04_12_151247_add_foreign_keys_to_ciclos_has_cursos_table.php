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
        Schema::table('ciclos_has_cursos', function (Blueprint $table) {
            $table->foreign(['ciclos_id'], 'fk_ciclos_has_cursos_ciclos1')->references(['id'])->on('ciclos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['cursos_id'], 'fk_ciclos_has_cursos_cursos1')->references(['id'])->on('cursos')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciclos_has_cursos', function (Blueprint $table) {
            $table->dropForeign('fk_ciclos_has_cursos_ciclos1');
            $table->dropForeign('fk_ciclos_has_cursos_cursos1');
        });
    }
};
