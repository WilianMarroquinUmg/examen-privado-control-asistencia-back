<?php

namespace Database\Seeders\bases;

use App\Models\User;
use App\Models\UserEstado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Para ejecutar este seeder: php artisan db:seed --class="Database\\Seeders\\bases\\UserSeeder"
     * @return void
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'primer_nombre' => 'Admin',
                'segundo_nombre' => '',
                'primer_apellido' => 'Admin',
                'segundo_apellido' => '',
                'usuario' => 'admin',
                'carnet' => '0000-00-0000',
                'estado_id' => 1,
                'password' => Hash::make('12345'),
            ]
        );

        $alumnos = [
            ['1890-17-15352', 'ABMTONY JEOVANI', 'CASTAÑEDA RÍOS', 'acastanedar@miumg.edu.gt'],
            ['1890-19-9521', 'ERIKA ESMERALDA ARGENTINA', 'BOL CRUZ', 'ebolc4@miumg.edu.gt'],
            ['1890-20-544', 'JUANA', 'RAGUEX TZÚM', 'jraguext1@miumg.edu.gt'],
            ['1890-20-11489', 'MERCEDES AZUCENA', 'LÓPEZ PÉREZ', 'mlopezp58@miumg.edu.gt'],
            ['1890-21-2310', 'JOSHUA ABINADÍ', 'CIRILO ALEGRÍA', 'jciriloa@miumg.edu.gt'],
            ['1890-21-11156', 'LIS IVETTE', 'ROSALES COLINDREZ', 'lrosalesc3@miumg.edu.gt'],
            ['1890-21-13279', 'ESAÚ ABIMAEL', 'DE LA CRUZ', 'edelacruz@miumg.edu.gt'],
            ['1890-22-1413', 'BORIS ALEXANDER', 'QUIROA ORELLANA', 'bquiroao@miumg.edu.gt'],
            ['1890-22-5831', 'JOSHUA EDUARDO', 'GARCÍA REYES', 'jgarciar73@miumg.edu.gt'],
            ['1890-22-19957', 'MARJORIE SAMANTHA', 'GIRÓN MORALES', 'mgironm20@miumg.edu.gt'],
            ['1890-23-2681', 'JAVIER ALEXANDER', 'FAJARDO LÓPEZ', 'jfajardol3@miumg.edu.gt'],
            ['1890-23-2862', 'JOSUÉ FERNANDO', 'HICHO GARCÍA', 'jhichog@miumg.edu.gt'],
            ['1890-23-3193', 'AXEL ELIÚ', 'HERRERA SÁNCHEZ', 'aherreras3@miumg.edu.gt'],
            ['1890-23-3686', 'GLENDI PATRICIA', 'CAMPOS ORELLANA', 'gcamposo@miumg.edu.gt'],
            ['1890-23-4982', 'KEILY FABIOLA', 'ORELLANA MARROQUÍN', 'korellanam6@miumg.edu.gt'],
            ['1890-23-5704', 'OSCAR LEONEL', 'CRUZ PAREDES', 'ocruzp2@miumg.edu.gt'],
            ['1890-23-7082', 'GERSON GIOVANNI', 'ORELLANA VÉLIZ', 'gorellanav1@miumg.edu.gt'],
            ['1890-23-7107', 'FELIX DANIEL', 'FLORES ESTRADA', 'fflorese@miumg.edu.gt'],
            ['1890-23-7145', 'LUIS DAVID', 'AROCHE CONTRERAS', 'larochec2@miumg.edu.gt'],
            ['1890-23-7587', 'JOSÉ PABLO', 'CARÍAS FLORES', 'jcariasf@miumg.edu.gt'],
            ['1890-23-8474', 'DIDHYER ALEXANDER', 'ORTÍZ GUEVARA', 'dortizg9@miumg.edu.gt'],
            ['1890-23-10545', 'JOSUÉ DAVID', 'MORALES RAMÍREZ', 'jmoralesr51@miumg.edu.gt'],
            ['1890-23-10675', 'CINDY MAYTTÉ', 'RUANO CALDERÓN', 'cruanoc9@miumg.edu.gt'],
            ['1890-23-10832', 'MARÍA DE LOS ANGELES', 'LÓPEZ FAJARDO', 'mlopezf16@miumg.edu.gt'],
            ['1890-23-11970', 'DULCE MARÍA', 'PRADO VÁSQUEZ', 'dpradov@miumg.edu.gt'],
            ['1890-23-12105', 'ALBINO SEBASTIAN', 'ROSALES RUANO', 'arosalesr13@miumg.edu.gt'],
            ['1890-23-14827', 'MARIA YAMILET', 'LINDO PABLO', 'mlindop@miumg.edu.gt'],
            ['1890-23-15373', 'JOSÉ ALEXÁNDER', 'DIONICIO CÓRDOVA', 'jdionicioc1@miumg.edu.gt'],
            ['1890-23-15896', 'ERICK ROLANDO', 'RAMAZZINI MURALLES', 'eramazzinim@miumg.edu.gt'],
            ['1890-23-16029', 'ARMANDO CECILIO', 'MORALES SAGASTUME', 'amoraless32@miumg.edu.gt'],
            ['1890-23-16776', 'MADELIN JAZMÍN', 'CERÓN MOLINA', 'mceronm@miumg.edu.gt'],
            ['1890-23-16857', 'EDDY ADOLFO', 'CASTRO VÉLIZ', 'ecastrov5@miumg.edu.gt'],
            ['1890-23-18949', 'MARYORI RACHAEL', 'FAJARDO PAREDES', 'mfajardop1@miumg.edu.gt'],
            ['1890-23-18958', 'BRANDON JOSÉ', 'AQUINO ARRIVILLAGA', 'baquinoa2@miumg.edu.gt'],
            ['1890-23-22889', 'HUGO DAVID', 'MOSCOSO CASTRO', 'hmoscosoc1@miumg.edu.gt'],
            ['1890-23-23782', 'ANTHONY DAVID', 'MARTÍNEZ LEÓN', 'amartinezl12@miumg.edu.gt'],
        ];

        $passwordDefault = Hash::make('password123'); // Contraseña genérica para todos los alumnos

        foreach ($alumnos as $alumno) {
            $carnet = $alumno[0];
            $nombres = explode(' ', $alumno[1]);
            $apellidos = explode(' ', $alumno[2]);
            $email = $alumno[3];

            // Extraemos lo que va antes del @ para usarlo como 'usuario'
            $usuario = explode('@', $email)[0];

            User::firstOrCreate(
                ['email' => $email],
                [
                    'carnet' => $carnet,
                    // Tomamos el primer elemento para el primer nombre, el resto para el segundo
                    'primer_nombre' => array_shift($nombres) ?? '',
                    'segundo_nombre' => implode(' ', $nombres) ?? '',

                    // Lo mismo para los apellidos
                    'primer_apellido' => array_shift($apellidos) ?? '',
                    'segundo_apellido' => implode(' ', $apellidos) ?? '',

                    'usuario' => $usuario,
                    'estado_id' => 1, // Ajusta si usas otra constante
                    'password' => $passwordDefault,
                ]
            );
        }
    }
}
