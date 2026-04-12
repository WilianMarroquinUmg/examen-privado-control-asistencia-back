<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Database\Seeders\EspacioTrabajo\Seeder;

class TrabajoEspacioPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Trabajo Espacios',
            'Crear Trabajo Espacios',
            'Editar Trabajo Espacios',
            'Eliminar Trabajo Espacios',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'TrabajoEspacio',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::find(Rol::ADMIN);

        $admin->givePermissionTo($permisos);

    }

}
