<?php

namespace App\Http\Controllers\Api\admin\Configuraciones;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\Configuraciones\CreateMenuOpcionApiRequest;
use App\Http\Requests\Api\admin\Configuraciones\UpdateMenuOpcionApiRequest;
use App\Models\MenuOpcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class MenuOpcionApiController
 */
class MenuOpcionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Menu Opciones', only: ['index']),
            new Middleware('permission:Ver Menu Opciones', only: ['show', 'index', 'getOpcionesMenu']),
            new Middleware('permission:Crear Menu Opciones', only: ['store']),
            new Middleware('permission:Editar Menu Opciones', only: ['update', 'actualizarOrden']),
            new Middleware('permission:Eliminar Menu Opciones', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Menu_opciones.
     * GET|HEAD /menu-opcions
     */
    public function index(Request $request): JsonResponse
    {
        $menuOpcions = QueryBuilder::for(MenuOpcion::class)
            ->with([])
            ->allowedFilters([
                'titulo',
                'titulo_seccion',
                'icono',
                'ruta',
                'orden',
                'action',
                'subject',
                'parent_id'
            ])
            ->allowedSorts([
                'titulo',
                'titulo_seccion',
                'icono',
                'ruta',
                'orden',
                'action',
                'subject',
                'parent_id'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Padres()
            ->with('children')
            ->orderBy('orden', 'asc')
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($menuOpcions->toArray(), 'menu-opcions recuperados con éxito.');
    }

    /**
     * Store a newly created MenuOpcion in storage.
     * POST /menu-opcions
     */
    public function store(CreateMenuOpcionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $ultimaOpcion = MenuOpcion::orderBy('orden', 'desc')->first();

        $input['orden'] = $ultimaOpcion ? $ultimaOpcion->orden + 1 : 0;

        MenuOpcion::create($input);

        $opcionesMenu = MenuOpcion::Padres()
            ->with('children')
            ->orderBy('orden', 'asc')
            ->get();

        return $this->sendResponse($opcionesMenu->toArray(), 'MenuOpcion creado con éxito.');
    }

    /**
     * Display the specified MenuOpcion.
     * GET|HEAD /menu-opcions/{id}
     */
    public function show(MenuOpcion $menuOpcion)
    {
        return $this->sendResponse($menuOpcion->toArray(), 'MenuOpcion recuperado con éxito.');
    }

    /**
     * Update the specified MenuOpcion in storage.
     * PUT/PATCH /menu-opcions/{id}
     */
    public function update(UpdateMenuOpcionApiRequest $request, $id): JsonResponse
    {
        $menuopcion = MenuOpcion::findOrFail($id);
        $menuopcion->update($request->validated());

        $opcionesMenu = MenuOpcion::Padres()
            ->with('children')
            ->orderBy('orden', 'asc')
            ->get();

        return $this->sendResponse($opcionesMenu->toArray(), 'MenuOpcion actualizado con éxito.');
    }

    /**
     * Remove the specified MenuOpcion from storage.
     * DELETE /menu-opcions/{id}
     */
    public function destroy(MenuOpcion $menuOpcion): JsonResponse
    {
        $menuOpcion->delete();
        return $this->sendResponse(null, 'MenuOpcion eliminado con éxito.');
    }

    public function actualizarOrden(Request $request)
    {

        $opciones = $request->opciones;

        foreach ($opciones as $index => $menuOpcion) {

            MenuOpcion::where('id', $menuOpcion['id'])->update(['orden' => $index]);

        }

        $opcionesMenu = MenuOpcion::Padres()
            ->with('children')
            ->orderBy('orden', 'asc')
            ->get();

        return $this->sendResponse($opcionesMenu, 'Orden actualizado con éxito.');


    }

    public function getOpcionesMenu()
    {
        $opcionesMenu = MenuOpcion::Padres()
            ->with('children')
            ->orderBy('orden', 'asc')
            ->get();

        return $this->sendResponse($opcionesMenu, 'Opciones de menú recuperadas con éxito.');

    }
}
