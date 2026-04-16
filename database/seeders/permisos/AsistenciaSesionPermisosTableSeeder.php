<?php

namespace Database\Seeders\permisos;

use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class AsistenciaSesionPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Asistencia Sesiones',
            'Crear Asistencia Sesiones',
            'Editar Asistencia Sesiones',
            'Eliminar Asistencia Sesiones',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'AsistenciaSesion',
                'guard_name' => 'web',
            ]);
        }



    }

}
