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
        Schema::create('asistencia_configuraciones', function (Blueprint $table) {
            $table->bigIncrements('id')->unique('id_unique');
            $table->tinyInteger('requiere_ubicacion');
            $table->tinyInteger('requiere_prueba_vida');
            $table->tinyInteger('requiere_codigo_otp');
            $table->string('cantidad_tomas_requeridas', 45);
            $table->integer('minutos_tolerancia');
            $table->unsignedBigInteger('catedratico_id')->index('fk_asistencia_configuraciones_users1_idx');
            $table->unsignedBigInteger('facultad_ciclo_curso_id')->index('fk_asistencia_configuraciones_facultad_ciclo_cursos1_idx');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_configuraciones');
    }
};
