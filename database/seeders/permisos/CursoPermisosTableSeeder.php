<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class CursoPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Cursos',
            'Crear Cursos',
            'Editar Cursos',
            'Eliminar Cursos',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'Curso',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::find(Rol::ADMIN);

        $admin->givePermissionTo($permisos);

    }

}
