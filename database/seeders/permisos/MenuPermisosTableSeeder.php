<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class MenuPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [];

        $permisos[] = Permission::create(['name' => 'Ver Modulo Pensums', 'subject' => 'Pensum', 'guard_name' => 'api',]);
        $permisos[] = Permission::create(['name' => 'Listar Modulo Pensums', 'subject' => 'Pensum', 'guard_name' => 'api',]);
        $permisos[] = Permission::create(['name' => 'Listar Facultades', 'subject' => 'Facultad', 'guard_name' => 'api',]);
        $permisos[] = Permission::create(['name' => 'Listar Ciclos', 'subject' => 'Ciclo', 'guard_name' => 'api',]);
        $permisos[] = Permission::create(['name' => 'Listar Cursos', 'subject' => 'Curso', 'guard_name' => 'api',]);

        $admin = Rol::find(Rol::ADMIN);

        $admin->givePermissionTo($permisos);

    }

}
