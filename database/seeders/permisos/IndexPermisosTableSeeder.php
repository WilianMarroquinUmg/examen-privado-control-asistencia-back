<?php

namespace Database\Seeders\permisos;

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
            MenuPermisosTableSeeder::class,
            TrabajoEspacioPermisosTableSeeder::class
        ]);

    }

}
