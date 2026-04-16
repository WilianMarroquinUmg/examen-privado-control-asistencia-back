<?php

namespace Database\Seeders\permisos;

use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Database\Seeder;


class AsistenciaConfiguracionPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Asistencia Configuraciones',
            'Crear Asistencia Configuraciones',
            'Editar Asistencia Configuraciones',
            'Eliminar Asistencia Configuraciones',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'subject' => 'AsistenciaConfiguracion',
                'guard_name' => 'web',
            ]);
        }



    }

}
