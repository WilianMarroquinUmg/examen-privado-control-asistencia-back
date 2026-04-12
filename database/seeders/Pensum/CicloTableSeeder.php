<?php

namespace Database\Seeders\Pensum;

use App\Models\Pensum\Ciclo;
use Database\Seeders\permisos\CicloPermisosTableSeeder;
use Illuminate\Database\Seeder;

class CicloTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ciclo::firstOrCreate([
            'nombre' => 'Primero',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Segundo',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Tercero',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Cuarto',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Quinto',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Sexto',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Séptimo',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Octavo',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Noveno',
        ]);

        Ciclo::firstOrCreate([
            'nombre' => 'Décimo',
        ]);
    }
}
