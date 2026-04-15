<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class AsistenciaRegistroPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Asistencia Registros',
            'Crear Asistencia Registros',
            'Editar Asistencia Registros',
            'Eliminar Asistencia Registros',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'AsistenciaRegistro',
                'guard_name' => 'web',
            ]);
        }



    }

}
