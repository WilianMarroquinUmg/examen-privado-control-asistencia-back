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
        Schema::create('facultad_ciclos', function (Blueprint $table) {
            $table->bigIncrements('id')->unique('id_unique');
            $table->unsignedBigInteger('facultades_id')->index('fk_pensums_facultades1_idx');
            $table->unsignedBigInteger('ciclos_id')->index('fk_pensums_ciclos1_idx');

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facultad_ciclos');
    }
};
