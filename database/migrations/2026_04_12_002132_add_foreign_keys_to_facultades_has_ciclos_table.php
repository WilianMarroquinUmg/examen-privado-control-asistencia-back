<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facultades_has_ciclos', function (Blueprint $table) {
            $table->foreign('facultades_id', 'fk_facultades_ciclos_fac_id')
                ->references('id')->on('facultades')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('ciclos_id', 'fk_facultades_ciclos_cic_id')
                ->references('id')->on('ciclos')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('facultades_has_ciclos', function (Blueprint $table) {
            $table->dropForeign('fk_facultades_ciclos_fac_id');
            $table->dropForeign('fk_facultades_ciclos_cic_id');
        });
    }
};
