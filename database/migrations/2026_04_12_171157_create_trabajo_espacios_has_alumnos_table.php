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
        Schema::create('trabajo_espacios_has_alumnos', function (Blueprint $table) {
            $table->unsignedBigInteger('trabajo_espacios_id');
            $table->unsignedBigInteger('users_id');

            $table->foreign('trabajo_espacios_id')
                ->references('id')
                ->on('trabajo_espacios')
                ->onDelete('cascade');

            $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary(['trabajo_espacios_id', 'users_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo_espacios_has_alumnos');
    }
};
