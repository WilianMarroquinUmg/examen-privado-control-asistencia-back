<?php

namespace Database\Seeders\permisos;

use Illuminate\Database\Seeder;

class IndexPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder php artisan db:seed --class="Database\Seeders\permisos\IndexPermisosTableSeeder"
     * @return void
     */
    public function run()
    {

        $this->call([
            PermisosBasicosTableSeeder::class,
            FacultadPermisosTableSeeder::class,
            CicloPermisosTableSeeder::class,
            CursoPermisosTableSeeder::class,
            MenuPermisosTableSeeder::class,
            TrabajoEspacioPermisosTableSeeder::class,
            AsistenciaConfiguracionPermisosTableSeeder::class,
            AsistenciaSesionPermisosTableSeeder::class,
            AsistenciaSesionTomaPermisosTableSeeder::class,
            AsistenciaRegistroPermisosTableSeeder::class,
        ]);

    }

}
