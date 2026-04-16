<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class MenuPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder php artisan db:seed --class="Database\Seeders\permisos\MenuPermisosTableSeeder"
     * @return void
     */
    public function run()
    {

        Permission::firstOrCreate(['name' => 'Listar Crear Espacio', 'subject' => 'Espacio', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Mis Espacios', 'subject' => 'Espacio', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Ver Modulo Pensums', 'subject' => 'Pensum', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Modulo Pensums', 'subject' => 'Pensum', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Facultades', 'subject' => 'Facultad', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Ciclos', 'subject' => 'Ciclo', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Cursos', 'subject' => 'Curso', 'guard_name' => 'web',]);
        Permission::firstOrCreate(['name' => 'Listar Certificar Fotos', 'subject' => 'Certificación', 'guard_name' => 'web',]);
    }

}
