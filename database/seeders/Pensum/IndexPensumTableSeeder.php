<?php

namespace Database\Seeders\Pensum;

use App\Models\Pensum\Ciclo;
use App\Models\Pensum\Facultad;
use Database\Seeders\permisos\CicloPermisosTableSeeder;
use Illuminate\Database\Seeder;

class IndexPensumTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\Pensum\IndexPensumTableSeeder"
     * @return void
     */
    public function run()
    {
        $this->call([
            CicloTableSeeder::class,
            CursoTableSeeder::class,
            FacultadTableSeeder::class
        ]);
    }
}
