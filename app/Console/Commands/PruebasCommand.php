<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class PruebasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebas:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

//        $user = User::with('fotoCertificada')
//        ->find(38);
//
//        dd($user->toArray());


        $todo = $this->getAllModelTypes();

        dd($todo);

//        dd($user->getMedia('avatars')->last()->getUrl('thumb24'));

    }

    public function getAllModelTypes()
    {
        // 1. Definir la ruta donde están tus modelos
        $path = app_path('Models');

        // 2. Obtener todos los archivos .php en esa carpeta
        $files = File::allFiles($path);

        $modelTypes = collect($files)->map(function ($file) {
            // Convertir la ruta del archivo en el Namespace del modelo
            // Ejemplo: app/Models/User.php -> App\Models\User
            $relativePath = $file->getRelativePathname();
            $class = 'App\\Models\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);

            // Validar que la clase existe y es un modelo de Eloquent
            if (class_exists($class)) {
                $reflection = new ReflectionClass($class);
                if ($reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class) && !$reflection->isAbstract()) {
                    return $class;
                }
            }

            return null;
        })->filter()->values();

        return $modelTypes;
    }
}
