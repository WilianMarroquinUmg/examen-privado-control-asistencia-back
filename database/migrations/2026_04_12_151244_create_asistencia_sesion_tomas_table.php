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
        Schema::create('asistencia_sesion_tomas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('hora_apertura');
            $table->dateTime('hora_cierre');
            $table->string('codito_otp', 10)->nullable();
            $table->integer('numero_toma');
            $table->unsignedBigInteger('sesion_id')->index('fk_asistencia_sesion_tomas_asistencia_sessiones1_idx');
            $table->string('longitud_origen', 50)->nullable();
            $table->string('latitud_origen', 50)->nullable();
            $table->integer('radio_metros')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_sesion_tomas');
    }
};
