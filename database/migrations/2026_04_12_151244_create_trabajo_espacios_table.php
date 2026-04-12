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
        Schema::create('trabajo_espacios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('catedratico_id')->index('fk_trabajo_espacios_users1_idx');
            $table->unsignedBigInteger('facultad_id')->index('fk_trabajo_espacios_facultades1_idx');
            $table->unsignedBigInteger('ciclo_id')->index('fk_trabajo_espacios_ciclos1_idx');
            $table->unsignedBigInteger('curso_id')->index('fk_trabajo_espacios_cursos1_idx');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo_espacios');
    }
};
