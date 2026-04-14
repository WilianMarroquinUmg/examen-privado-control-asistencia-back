<?php

namespace Database\Seeders\bases;

use Database\Seeders\RolesTableSeeder;
use Illuminate\Database\Seeder;

class IndexTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder, se debe ejecutar el comando: php artisan db:seed --class="Database\Seeders\bases\IndexTableSeeder"
     */
    public function run(): void
    {

        $this->call([
            UsersEstadosTableSeeder::class,
            UserSeeder::class,
            RolesTableSeeder::class,
            MenuOpcionesTableSeeder::class,
            ConfiguracionesTableSeeder::class,
        ]);

    }
}
