<?php

namespace Database\Seeders\permisos;

use App\Models\Rol;
use App\Models\Permission;
use Illuminate\Database\Seeder;


class PermisosBasicosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Permisos para administrar las Opciones Del Menu.
        Permission::firstOrCreate(['name' => 'Ver Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);

        // Permisos para los Usuarios.
        Permission::firstOrCreate(['name' => 'Ver Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ver Perfil Usuario', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Actualizar Perfil Usuario', 'subject' => 'User', 'guard_name' => 'web']);

        // Permisos para los Permisos.
        Permission::firstOrCreate(['name' => 'Ver Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);

        // Permisos para los Roles.
        Permission::firstOrCreate(['name' => 'Ver Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);

        // Permisos para configuraciones
        Permission::firstOrCreate(['name' => 'Ver Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Actualizar Configuraciones Generales', 'subject' => 'Configuracion', 'guard_name' => 'web']);

        // Permisos Básicos
        Permission::firstOrCreate(['name' => 'Listar Inicio', 'subject' => 'Inicio', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ver Menu Preferencias', 'subject' => 'Preferencias', 'guard_name' => 'web']);

        // Permisos para modulo usuarios.
        Permission::firstOrCreate(['name' => 'Ver Modulo Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);

        //Permisos para Estados de los usuarios
        Permission::firstOrCreate(['name' => 'Listar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ver Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Crear Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Editar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Eliminar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);

        // Permisos para Módulo de configuración.
        Permission::firstOrCreate(['name' => 'Ver Modulo Configuracion', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Configuraciones Generales', 'subject' => 'Configuracion', 'guard_name' => 'web']);

        // Permisos para Modulo de desarrollo.
        Permission::firstOrCreate(['name' => 'Ver Modulo Desarrollo', 'subject' => 'Desarrollo', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Listar Componentes', 'subject' => 'Desarrollo', 'guard_name' => 'web']);

    }

}
