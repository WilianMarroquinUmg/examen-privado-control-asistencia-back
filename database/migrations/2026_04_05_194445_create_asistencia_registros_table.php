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
        Schema::create('asistencia_registros', function (Blueprint $table) {
            $table->bigIncrements('id')->unique('id_unique');
            $table->time('hora_registro');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 10, 8)->nullable();
            $table->string('foto_evidencia_url', 120)->nullable();
            $table->decimal('aws_liveness_score', 10, 0)->nullable();
            $table->tinyInteger('fue_aprobada');
            $table->unsignedBigInteger('toma_asistencia_id')->index('fk_asistencia_registros_asistencia_sesion_tomas1_idx');
            $table->unsignedBigInteger('alumno_id')->index('fk_asistencia_registros_users1_idx');
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
        Schema::dropIfExists('asistencia_registros');
    }
};
