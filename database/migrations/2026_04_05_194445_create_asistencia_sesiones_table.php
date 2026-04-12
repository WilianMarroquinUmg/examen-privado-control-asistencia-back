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
        Schema::create('asistencia_sesiones', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->date('fecha');
            $table->enum('estado', ['En curso', 'Finalizada']);
            $table->unsignedBigInteger('catedratico_id')->index('fk_asistencia_sessiones_users1_idx');
            $table->unsignedBigInteger('facultad_id')->index('fk_asistencia_sessiones_facultad_idx');
            $table->unsignedBigInteger('ciclo_id')->index('fk_asistencia_sessiones_ciclo_idx');
            $table->unsignedBigInteger('curso_id')->index('fk_asistencia_sessiones_curso_idx');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_sesiones');
    }
};
