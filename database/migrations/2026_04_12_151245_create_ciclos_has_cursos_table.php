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
        Schema::create('ciclos_has_cursos', function (Blueprint $table) {
            $table->unsignedBigInteger('facultad_id');
            $table->unsignedBigInteger('ciclo_id')->index('fk_ciclos_has_cursos_ciclos1_idx');
            $table->unsignedBigInteger('curso_id')->index('fk_ciclos_has_cursos_cursos1_idx');

            $table->primary(['facultad_id', 'ciclo_id', 'curso_id'], 'pk_facultad_ciclo_curso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciclos_has_cursos');
    }
};
