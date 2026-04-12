<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class FacultadPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Facultades',
            'Crear Facultades',
            'Editar Facultades',
            'Eliminar Facultades',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'Facultad',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::find(Rol::ADMIN);

        $admin->givePermissionTo($permisos);

    }

}
