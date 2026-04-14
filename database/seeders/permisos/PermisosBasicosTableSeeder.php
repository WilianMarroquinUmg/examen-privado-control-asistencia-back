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
        Permission::create(['name' => 'Ver Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);

        // Permisos para los Usuarios.
        Permission::create(['name' => 'Ver Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Ver Perfil Usuario', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Actualizar Perfil Usuario', 'subject' => 'User', 'guard_name' => 'web']);

        // Permisos para los Permisos.
        Permission::create(['name' => 'Ver Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);

        // Permisos para los Roles.
        Permission::create(['name' => 'Ver Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);

        // Permisos para configuraciones
        Permission::create(['name' => 'Ver Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Actualizar Configuraciones Generales', 'subject' => 'Configuracion', 'guard_name' => 'web']);

        // Permisos Básicos
        Permission::create(['name' => 'Listar Inicio', 'subject' => 'Inicio', 'guard_name' => 'web']);
        Permission::create(['name' => 'Ver Menu Preferencias', 'subject' => 'Preferencias', 'guard_name' => 'web']);

        // Permisos para modulo usuarios.
        Permission::create(['name' => 'Ver Modulo Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Usuarios', 'subject' => 'User', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Roles', 'subject' => 'Rol', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Permisos', 'subject' => 'Permission', 'guard_name' => 'web']);

        //Permisos para Estados de los usuarios
        Permission::create(['name' => 'Listar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::create(['name' => 'Ver Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::create(['name' => 'Crear Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::create(['name' => 'Editar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);
        Permission::create(['name' => 'Eliminar Usuario Estados', 'subject' => 'UserEstado', 'guard_name' => 'web']);

        // Permisos para Módulo de configuración.
        Permission::create(['name' => 'Ver Modulo Configuracion', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Menu Opciones', 'subject' => 'Menu Opcion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Configuraciones Generales', 'subject' => 'Configuracion', 'guard_name' => 'web']);

        // Permisos para Modulo de desarrollo.
        Permission::create(['name' => 'Ver Modulo Desarrollo', 'subject' => 'Desarrollo', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Configuraciones', 'subject' => 'Configuracion', 'guard_name' => 'web']);
        Permission::create(['name' => 'Listar Componentes', 'subject' => 'Desarrollo', 'guard_name' => 'web']);

    }

}
