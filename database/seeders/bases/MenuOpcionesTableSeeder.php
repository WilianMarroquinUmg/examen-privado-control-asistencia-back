<?php

namespace Database\Seeders\bases;

use App\Models\MenuOpcion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuOpcionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\bases\MenuOpcionesTableSeeder"
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MenuOpcion::truncate();

        // Definimos la estructura visualmente
        $menus = [
            [
                "titulo" => "Inicio",
                "icono" => "ri-home-8-line",
                "ruta" => "index",
                "action" => "Listar Inicio",
                "subject" => "Inicio",
            ],
            [
                "titulo" => "Crear Espacio",
                "icono" => "ri-add-large-line",
                "ruta" => "crear-espacio",
                "action" => "Listar Crear Espacio",
                "subject" => "Espacio",
            ],
            [
                "titulo_seccion" => "Administración",
                "action" => "Ver Modulo Usuarios",
                "subject" => "User",
            ],
            [
                "titulo" => "Pénsums",
                "icono" => "ri-book-2-line",
                "action" => "Ver Modulo Pensums",
                "subject" => "Pensum",
                "submenus" => [
                    [
                        "titulo" => "Administrar",
                        "icono" => "ri-book-2-line",
                        "ruta" => "administrar-pensums",
                        "action" => "Listar Modulo Pensums",
                        "subject" => "Pensum",
                    ],
                    [
                        "titulo" => "Facultades",
                        "icono" => "ri-building-2-line",
                        "ruta" => "administrar-pensums-facultades",
                        "action" => "Listar Facultades",
                        "subject" => "Facultad",
                    ],
                    [
                        "titulo" => "Ciclos",
                        "icono" => "ri-calendar-2-line",
                        "ruta" => "administrar-pensums-ciclos",
                        "action" => "Listar Ciclos",
                        "subject" => "Ciclo",
                    ],
                    [
                        "titulo" => "Cursos",
                        "icono" => "ri-book-open-line",
                        "ruta" => "administrar-pensums-cursos",
                        "action" => "Listar Cursos",
                        "subject" => "Curso",
                    ]
                ]
            ],
            [
                "titulo" => "Modulo Usuarios",
                "icono" => "ri-group-line",
                "action" => "Ver Modulo Usuarios",
                "subject" => "User",
                "submenus" => [
                    [
                        "titulo" => "Usuarios",
                        "icono" => "ri-list-ordered-2",
                        "ruta" => "admin-modulo-usuarios-usuarios",
                        "action" => "Listar Usuarios",
                        "subject" => "User",
                    ],
                    [
                        "titulo" => "Roles",
                        "icono" => "ri-folder-shield-2-line",
                        "ruta" => "admin-modulo-usuarios-roles",
                        "action" => "Listar Roles",
                        "subject" => "Rol",
                    ],
                    [
                        "titulo" => "Permisos",
                        "icono" => "ri-file-shield-2-fill",
                        "ruta" => "admin-modulo-usuarios-permisos",
                        "action" => "Listar Permisos",
                        "subject" => "Permission",
                    ],
                    [
                        "titulo" => "Estados de usuarios",
                        "icono" => "ri-folder-user-fill",
                        "ruta" => "admin-modulo-usuarios-usuario-estados",
                        "action" => "Listar Usuario Estados",
                        "subject" => "UserEstado",
                    ]
                ]
            ],
            [
                "titulo" => "Configuraciones",
                "icono" => "ri-folder-settings-fill",
                "action" => "Ver Modulo Configuracion",
                "subject" => "Configuracion",
                "submenus" => [
                    [
                        "titulo" => "Opciones Menu",
                        "icono" => "ri-apps-2-add-line",
                        "ruta" => "admin-configuraciones-menu",
                        "action" => "Listar Menu Opciones",
                        "subject" => "Menu Opcion",
                    ],
                    [
                        "titulo" => "Generales",
                        "icono" => "ri-settings-3-fill",
                        "ruta" => "admin-configuraciones-generales",
                        "action" => "Listar Configuraciones Generales",
                        "subject" => "Configuracion",
                    ]
                ]
            ],
            [
                "titulo_seccion" => "Modulo Programación",
                "action" => "Ver Modulo Desarrollo",
                "subject" => "Desarrollo",
            ],
            [
                "titulo" => "Developers",
                "icono" => "ri-tools-fill",
                "ruta" => "second-page",
                "action" => "Ver Modulo Desarrollo",
                "subject" => "Desarrollo",
                "submenus" => [
                    [
                        "titulo" => "Configuraciones",
                        "icono" => "ri-settings-5-fill",
                        "ruta" => "dev-configuraciones",
                        "action" => "Listar Configuraciones",
                        "subject" => "Configuracion",
                    ],
                    [
                        "titulo" => "Componentes",
                        "icono" => "ri-code-box-line",
                        "ruta" => "dev-componentes",
                        "action" => "Listar Componentes",
                        "subject" => "Desarrollo",
                    ]
                ]
            ]
        ];

        // Magia para insertar todo automáticamente
        $ordenPadre = 0;

        foreach ($menus as $menuData) {
            // Extraemos los submenús si existen, y los quitamos del arreglo principal
            $submenus = $menuData['submenus'] ?? [];
            unset($menuData['submenus']);

            // Asignamos el orden automáticamente
            $menuData['orden'] = $ordenPadre++;
            $menuData['parent_id'] = null; // Es un padre

            // Creamos el menú padre y obtenemos la instancia con su ID recién creado
            $padre = MenuOpcion::create($menuData);

            // Si tiene hijos, los iteramos
            if (!empty($submenus)) {
                $ordenHijo = 0;
                foreach ($submenus as $hijoData) {
                    $hijoData['orden'] = $ordenHijo++;
                    $hijoData['parent_id'] = $padre->id; // Aquí inyectamos el ID correcto

                    MenuOpcion::create($hijoData);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
