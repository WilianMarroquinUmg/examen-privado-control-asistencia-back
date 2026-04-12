<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_has_cursos', function (Blueprint $table) {
            $table->unsignedBigInteger('ciclos_id');
            $table->unsignedBigInteger('cursos_id');

            $table->primary(['ciclos_id', 'cursos_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_has_cursos');
    }
};
