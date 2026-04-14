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
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->enum('estado', ['En curso', 'Finalizada']);
            $table->unsignedBigInteger('espacio_id')->index('fk_asistencia_sesiones_trabajo_espacios1_idx');
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
