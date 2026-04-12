<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ciclos_has_cursos', function (Blueprint $table) {
            $table->foreign('ciclos_id', 'fk_ciclos_cursos_cic_id')
                ->references('id')->on('ciclos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('cursos_id', 'fk_ciclos_cursos_cur_id')
                ->references('id')->on('cursos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ciclos_has_cursos', function (Blueprint $table) {
            $table->dropForeign('fk_ciclos_cursos_cic_id');
            $table->dropForeign('fk_ciclos_cursos_cur_id');
        });
    }
};
