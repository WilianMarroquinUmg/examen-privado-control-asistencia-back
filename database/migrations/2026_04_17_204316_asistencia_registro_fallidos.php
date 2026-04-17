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
        Schema::create('asistencia_intentos_fallidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('users');
            $table->foreignId('toma_asistencia_id')->constrained('asistencia_sesion_tomas');
            $table->string('motivo'); // Ej: 'OTP_INCORRECTO', 'GPS_FUERA_DE_RANGO', 'LIVENESS_FALLO'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_intentos_fallidos');
    }
};
