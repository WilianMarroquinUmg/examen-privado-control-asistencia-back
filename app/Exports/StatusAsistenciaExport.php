<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StatusAsistenciaExport implements FromArray, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $datos;

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    public function array(): array
    {
        return $this->datos;
    }

    // Títulos de las columnas en el Excel/PDF
    public function headings(): array
    {
        return [
            'Carnet',
            'Estudiante',
            'Sesiones Asistidas',
            'Total Sesiones',
            'Porcentaje',
            'Estado'
        ];
    }

    // Acomodamos la data fila por fila mapeando lo que mandó el Frontend
    public function map($row): array
    {
        Log::info('Row: ' . print_r($row, true));
        $nombre = $row['nombre'] ?? $row['nombre_completo'] ?? '';
        $asistidas = $row['asistencias_completas'] == 0 ? '0' : '0';
        $total = $row['total_sesiones'] ?? $row['estadisticas']['total_tomas_curso'] ?? 0;
        $porcentaje = $row['porcentaje'] ?? $row['estadisticas']['porcentaje_asistencia'] ?? 0;
        $estado = $row['estado'] ?? $row['estadisticas']['estado_riesgo'] ?? 'Desconocido';

        return [
            $row['carnet'] ?? '',
            $nombre,
            $asistidas,
            $total,
            $porcentaje . '%',
            $estado
        ];
    }
}
