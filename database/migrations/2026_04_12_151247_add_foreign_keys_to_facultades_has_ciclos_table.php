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
        Schema::table('facultades_has_ciclos', function (Blueprint $table) {
            $table->foreign(['ciclos_id'], 'fk_facultades_has_ciclos_ciclos1')->references(['id'])->on('ciclos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['facultades_id'], 'fk_facultades_has_ciclos_facultades1')->references(['id'])->on('facultades')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facultades_has_ciclos', function (Blueprint $table) {
            $table->dropForeign('fk_facultades_has_ciclos_ciclos1');
            $table->dropForeign('fk_facultades_has_ciclos_facultades1');
        });
    }
};
