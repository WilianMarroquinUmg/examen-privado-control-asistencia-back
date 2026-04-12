<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facultades_has_ciclos', function (Blueprint $table) {
            $table->unsignedBigInteger('facultades_id');
            $table->unsignedBigInteger('ciclos_id');

            $table->primary(['facultades_id', 'ciclos_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facultades_has_ciclos');
    }
};
