<?php

namespace Database\Seeders\bases;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesPermisosBaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Role::truncate();
        Permission::truncate();

        //Crea los roles por defecto del sistema.
        $rolAdministrador = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        $rolEmpleado = Role::create(['name' => 'Empleado', 'guard_name' => 'web']);
        $rolProgramador = Role::create(['name' => 'Programador', 'guard_name' => 'web']);
        $rolEstudiante = Role::create(['name' => 'Estudiante', 'guard_name' => 'web']);
        $catedratico = Role::create(['name' => 'Catedrático', 'guard_name' => 'web']);

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

        // Asignar todos los permisos al rol Administrador.
        $rolAdministrador->syncPermissions([
            'Ver Menu Opciones',      // Permite ver el menú de opciones
            'Crear Menu Opciones',    // Permite crear opciones en el menú
            'Editar Menu Opciones',   // Permite editar las opciones del menú
            'Eliminar Menu Opciones', // Permite eliminar opciones del menú
            'Ver Usuarios',           // Permite ver Usuarios
            'Crear Usuarios',         // Permite crear Usuarios
            'Editar Usuarios',        // Permite editar Usuarios
            'Eliminar Usuarios',      // Permite eliminar usuarios
            'Ver Permisos',           // Permite ver Permisos
            'Crear Permisos',         // Permite crear Permisos
            'Editar Permisos',        // Permite editar Permisos
            'Eliminar Permisos',      // Permite eliminar permisos
            'Ver Roles',              // Permite ver Roles
            'Crear Roles',            // Permite crear Roles
            'Editar Roles',           // Permite editar Roles
            'Eliminar Roles',         // Permite eliminar roles
            'Listar Inicio',          // Solo permisos básicos para la página de inicio
            'Ver Menu Preferencias',  // Solo permisos básicos para el menú de preferencias
            'Ver Modulo Usuarios',    // Permite ver el módulo de usuarios
            'Listar Usuarios',        // Permite listar usuarios
            'Listar Roles',           // Permite listar roles
            'Listar Permisos',        // Permite listar permisos
            'Ver Modulo Configuracion',// Permite ver el módulo de configuración
            'Listar Menu Opciones',   // Permite listar el menú de opciones
            'Listar Configuraciones Generales', // Permite listar configuraciones
            'Actualizar Configuraciones Generales', // Permite listar configuraciones
            'Actualizar Perfil Usuario' , // Permite actualizar el perfil del usuario
            'Ver Perfil Usuario' , // Permite ver el perfil del usuario
            'Listar Usuario Estados' , // Permite ver el modulo de estados de usuario
            'Ver Usuario Estados' , // Permite ver un usuario especifico
            'Crear Usuario Estados' , // Permite crear un nuevo usuario
            'Editar Usuario Estados' , // Permite editar un usuario
            'Eliminar Usuario Estados' , // Permite eliminar un usuario
        ]);

        // Asignación de permisos al rol Empleado.
        $rolEmpleado->syncPermissions([
            'Ver Menu Preferencias', // Solo permisos básicos para el menú de preferencias
            'Listar Inicio',          // Solo permisos básicos para la página de inicio
            'Ver Menu Opciones',      // Permite ver el menú de opciones
            'Actualizar Perfil Usuario' , // Permite actualizar el perfil del usuario
            'Ver Perfil Usuario' , // Permite ver el perfil del usuario
        ]);

        // Asignación de permisos al rol Programador.
        $rolProgramador->syncPermissions([
            'Listar Inicio', // Solo permisos básicos para la página de inicio
            'Ver Menu Preferencias', // Solo permisos básicos para el menú de preferencias
            'Ver Menu Opciones',      // Permite ver el menú de opciones
            'Ver Modulo Desarrollo', // Permite ver el módulo de desarrollo
            'Listar Configuraciones' ,        // Permite ver ejemplos
            'Listar Componentes' ,        // Permite ver ejemplos
            'Ver Configuraciones' ,        // Permite ver ejemplos
            'Crear Configuraciones' ,        // Permite ver ejemplos
            'Editar Configuraciones' ,        // Permite ver ejemplos
            'Eliminar Configuraciones' ,        // Permite ver ejemplos
            'Actualizar Perfil Usuario' , // Permite actualizar el perfil del usuario
            'Ver Perfil Usuario' , // Permite ver el perfil del usuario
        ]);

        // El super admin obtiene todos los permisos por defecto.
        Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);

        User::find(1)->assignRole('Super Admin');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

