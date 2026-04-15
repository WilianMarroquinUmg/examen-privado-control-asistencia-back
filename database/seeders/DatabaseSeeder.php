<?php

namespace Database\Seeders;

use Database\Seeders\bases\IndexTableSeeder;
use Database\Seeders\Pensum\IndexPensumTableSeeder;
use Database\Seeders\permisos\IndexPermisosTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Para ejecutar este seeder, se debe ejecutar el comando: php artisan db:seed DatabaseSeeder
     */
    public function run(): void
    {
        $this->call(IndexPermisosTableSeeder::class);
        $this->call(IndexTableSeeder::class);
        $this->call(IndexPensumTableSeeder::class);
        $this->call(RolesTableSeeder::class);

    }
}
