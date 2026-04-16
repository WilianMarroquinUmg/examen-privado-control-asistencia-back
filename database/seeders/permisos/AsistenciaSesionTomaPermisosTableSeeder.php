<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class AsistenciaSesionTomaPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Asistencia Sesion Tomas',
            'Crear Asistencia Sesion Tomas',
            'Editar Asistencia Sesion Tomas',
            'Eliminar Asistencia Sesion Tomas',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'AsistenciaSesionToma',
                'guard_name' => 'web',
            ]);
        }



    }

}
