<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Database\Seeders\Pensum\CicloTableSeeder;
use Illuminate\Database\Seeder;

class IndexPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

   $this->call([
            FacultadPermisosTableSeeder::class,
            CicloPermisosTableSeeder::class,
            CursoPermisosTableSeeder::class,
        ]);

    }

}
