<?php

namespace Database\Seeders\Pensum;

use App\Models\Pensum\Curso;
use Illuminate\Database\Seeder;

class CursoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Curso::firstOrCreate([
            'nombre' => 'Programación I',
            'codigo' => '001',
            'identificacion_institucional' => 'PROG1',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Programación II',
            'codigo' => '002',
            'identificacion_institucional' => 'PROG2',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Estructura de Datos',
            'codigo' => '003',
            'identificacion_institucional' => 'ESTDAT',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Algoritmos',
            'codigo' => '004',
            'identificacion_institucional' => 'ALGOR',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Bases de Datos',
            'codigo' => '005',
            'identificacion_institucional' => 'BD',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Sistemas Operativos',
            'codigo' => '006',
            'identificacion_institucional' => 'SO',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Redes de Computadoras',
            'codigo' => '007',
            'identificacion_institucional' => 'REDES',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Ingeniería de Software',
            'codigo' => '008',
            'identificacion_institucional' => 'IS',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Inteligencia Artificial',
            'codigo' => '009',
            'identificacion_institucional' => 'IA',
        ]);

        Curso::firstOrCreate([
            'nombre' => 'Seguridad Informática',
            'codigo' => '010',
            'identificacion_institucional' => 'SEGINFO',
        ]);
    }
}
