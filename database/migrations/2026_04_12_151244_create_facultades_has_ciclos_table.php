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
        Schema::create('facultades_has_ciclos', function (Blueprint $table) {
            $table->unsignedBigInteger('facultades_id')->index('fk_facultades_has_ciclos_facultades1_idx');
            $table->unsignedBigInteger('ciclos_id')->index('fk_facultades_has_ciclos_ciclos1_idx');

            $table->primary(['facultades_id', 'ciclos_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facultades_has_ciclos');
    }
};
