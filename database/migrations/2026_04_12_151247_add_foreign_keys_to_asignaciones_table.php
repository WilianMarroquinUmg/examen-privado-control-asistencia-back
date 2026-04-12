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
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->foreign(['espacio_id'], 'fk_asignaciones_trabajo_espacios1')->references(['id'])->on('trabajo_espacios')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['alumno_id'], 'fk_asignaciones_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropForeign('fk_asignaciones_trabajo_espacios1');
            $table->dropForeign('fk_asignaciones_users1');
        });
    }
};
