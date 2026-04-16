<?php
namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings; // <--- 1. Importante
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // <--- Extra: Para que las columnas se ajusten solas

class DataTableExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $query;
    protected $columns;

    public function __construct($query, $columns)
    {
        $this->query = $query;
        $this->columns = $columns;
    }

    public function query()
    {
        return $this->query;
    }

    // MAPEO DE DATOS (FILAS)
    public function map($row): array
    {
        $datos = array_map(function ($col) use ($row) {
            // data_get es vital para relaciones como 'gerencia.nombre'
            return data_get($row, $col['key']);
        }, $this->columns);

        Log::info('Datos: ' . print_r($datos, true));

        return array_map(function ($col) use ($row) {
            // data_get es vital para relaciones como 'gerencia.nombre'
            return data_get($row, $col['key']);
        }, $this->columns);
    }

    // ENCABEZADOS (HEADERS)
    public function headings(): array
    {
        $columns = array_column($this->columns, 'title');

        Log::info('Headings: ' . print_r($columns, true));
        // <--- 2. AQUI ESTABA EL DETALLE
        // Tu array de columnas viene con 'title' desde el front, no con 'label'
        return array_column($this->columns, 'title');

    }
}
