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
        Schema::create('facultad_ciclo_cursos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('facultad_ciclo_id')->index('fk_pensum_cursos_pensum_ciclos1_idx');
            $table->unsignedBigInteger('cursos_id')->index('fk_pensum_cursos_cursos1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facultad_ciclo_cursos');
    }
};
