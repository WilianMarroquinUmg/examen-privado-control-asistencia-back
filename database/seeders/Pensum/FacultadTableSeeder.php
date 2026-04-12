<?php

namespace Database\Seeders\Pensum;

use App\Models\Pensum\Facultad;
use Illuminate\Database\Seeder;

class FacultadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $facultades = [
            ['nombre' => 'Ciencias Médicas', 'codigo' => '002'],
            ['nombre' => 'Ciencias Jurídicas y Sociales', 'codigo' => '003'],
            ['nombre' => 'Arquitectura y Diseño', 'codigo' => '004'],
            ['nombre' => 'Ciencias Económicas y Empresariales', 'codigo' => '005'],
            ['nombre' => 'Humanidades y Educación', 'codigo' => '006'],
            ['nombre' => 'Ciencias Agrícolas y Ambientales', 'codigo' => '007'],
            ['nombre' => 'Odontología', 'codigo' => '008'],
            ['nombre' => 'Ciencias Químicas y Farmacia', 'codigo' => '009'],
            ['nombre' => 'Psicología Clínica', 'codigo' => '010'],
            ['nombre' => 'Ingeniería Civil', 'codigo' => '011'],
        ];

        foreach ($facultades as $facultad) {
            Facultad::firstOrCreate([
                'codigo' => $facultad['codigo']
            ], [
                'nombre' => $facultad['nombre']
            ]);
        }

    }
}
