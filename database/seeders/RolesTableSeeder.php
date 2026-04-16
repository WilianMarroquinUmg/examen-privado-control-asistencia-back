<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder, se debe ejecutar el comando: php artisan db:seed --class="Database\Seeders\RolesTableSeeder"
     * @return void
     */
    public function run()
    {
        $rolAdministrador = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $rolProgramador = Role::firstOrCreate(['name' => 'Programador', 'guard_name' => 'web']);
        $rolEstudiante = Role::firstOrCreate(['name' => 'Estudiante', 'guard_name' => 'web']);
        $catedratico = Role::firstOrCreate(['name' => 'Catedrático', 'guard_name' => 'web']);
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // Asignar todos los permisos al rol Administrador.
        $rolAdministrador->syncPermissions($this->todosLosPermisos());

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

        $catedratico->syncPermissions([
            'Listar Inicio',
            'Ver Menu Preferencias',
            'Listar Configuraciones',
            'Ver Configuraciones',
            'Actualizar Perfil Usuario',
            'Ver Perfil Usuario',
            'Listar Crear Espacio',
            'Listar Mis Espacios',
            'Ver Menu Opciones',
            'Ver Facultades',
            'Ver Ciclos',
            'Ver Cursos',
            'Ver Trabajo Espacios',
            'Crear Trabajo Espacios',
            'Ver Usuarios',
            'Ver Asistencia Sesiones',
            'Crear Asistencia Sesiones',
            'Ver Asistencia Sesion Tomas',
            'Crear Asistencia Sesion Tomas',
            'Ver Asistencia Registros',
            'Listar Certificar Fotos',
        ]);

        $rolEstudiante->syncPermissions([
            'Listar Inicio',
            'Ver Menu Preferencias',
            'Listar Configuraciones',
            'Ver Configuraciones',
            'Actualizar Perfil Usuario',
            'Ver Perfil Usuario',
            'Listar Ajustes Estudiante',
            'Ver Asistencia Sesion Tomas',
            'Crear Asistencia Registros',
            'Ver Menu Opciones',
        ]);

        User::find(1)->assignRole('Catedrático');

    }
    public function todosLosPermisos(): array
    {
        return Permission::all()->pluck('name')->toArray();
    }
}

