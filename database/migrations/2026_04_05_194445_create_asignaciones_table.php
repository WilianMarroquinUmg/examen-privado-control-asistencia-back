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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('alumno_id')->index('fk_asignaciones_users1_idx');
            $table->unsignedBigInteger('catedratico_id')->index('fk_asignaciones_users2_idx');
            $table->unsignedBigInteger('facultad_ciclo_curso_id')->index('fk_asignaciones_facultad_ciclo_cursos1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
