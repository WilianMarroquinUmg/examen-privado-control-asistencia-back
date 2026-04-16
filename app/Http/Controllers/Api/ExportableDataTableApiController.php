<?php


namespace App\Http\Controllers\Api;

use App\Exports\DataTableExport;
use App\Exports\StatusAsistenciaExport;
use App\Http\Controllers\AppBaseController;
use App\Traits\ExportableDataTableTrait;
use App\Traits\Plantillas\ManejaPlantillasTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;

class ExportableDataTableApiController extends AppBaseController
{

    use ExportableDataTableTrait;
    use ManejaPlantillasTrait;

    public static function middleware(): array
    {
        return [
            new Middleware('abilities:exportar tablas', only: [
                'exportarExcel',
                'exportarPdf',
                'wordToPdf',
            ]),
        ];
    }

    public function exportarExcel(Request $request)
    {
        try {
            $query = $this->construirQuery(
                $request->input('model'),
                $request->input('filters', []),
                $request->input('columns', [])
            );

            $columnasExportables = array_values(array_filter(
                $request->input('columns', []),
                fn($col) => filter_var($col['exportable'] ?? true, FILTER_VALIDATE_BOOLEAN)
            ));

            $format = $request->input('format', 'xlsx');

            $writer = $format === 'csv'
                ? Excel::CSV
                : Excel::XLSX;

            if ($format === 'csv') {
                config([
                    'excel.exports.csv.use_bom' => true,
                ]);
            }

            return (new DataTableExport($query, $columnasExportables))
                ->download("data.$format", $writer, [
                    'Content-Type' => $format === 'csv'
                        ? 'text/csv; charset=UTF-8'
                        : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        } catch (Exception $e) {
            return $this->sendError('Error al exportar Excel: ' . $e->getMessage());
        }
    }

    public function exportarPdf(Request $request)
    {
        try {
            $query = $this->construirQuery(
                $request->input('model'),
                $request->input('filters', []),
                $request->input('columns', [])
            );

            // Ojo: Limitamos los resultados para no saturar la RAM
            $data = $query->limit(1000)->get();
            $columnas = $this->getColumnasExportables($request->input('columns'));

            $user = $request->user()->nombre_corto;

            //obtener el nombre del modelo
            $nombreModelo = class_basename($request->input('model'));

            $pdf = Pdf::loadView('datatables_exports.export_pdf', [
                'data' => $data,
                'columns' => $columnas,
                'title' => $nombreModelo,
                'user' => $user,
            ]);

            $pdf->setPaper('a4', 'landscape');

            return $pdf->stream('reporte.pdf');

        } catch (Exception $e) {
            return $this->sendError('Error al exportar PDF: ' . $e->getMessage());
        }
    }

    public function wordToPdf(Request $request)
    {
        $ruta = $request->get('ruta');

        if (!$ruta || !file_exists($ruta)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }

        $pdfContent = $this->wordPathaPdf($ruta);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }


    public function exportarStatusAsistencia(Request $request)
    {
        try {
            $format = $request->query('format', 'xlsx');
            $datos = $request->input('data', []);

            if (empty($datos)) {
                return response()->json(['message' => 'No hay datos para exportar'], 400);
            }

            // ==========================================
            // 🚀 1. RUTA EXCLUSIVA PARA PDF (TU PLANTILLA)
            // ==========================================
            if ($format === 'pdf') {
                // A. Mapeamos y aplanamos la data para que tu vista Blade
                //    pueda leerla limpiamente con data_get()
                $dataParaPdf = collect($datos)->map(function($row) {
                    $asistidas = $row['asistencias_completas'] ?? $row['estadisticas']['tomas_asistidas'] ?? 0;
                    $total = $row['total_sesiones'] ?? $row['estadisticas']['total_tomas_curso'] ?? 0;
                    $porcentaje = $row['porcentaje'] ?? $row['estadisticas']['porcentaje_asistencia'] ?? 0;

                    return [
                        'carnet'     => $row['carnet'] ?? 'S/N',
                        'estudiante' => $row['nombre'] ?? $row['nombre_completo'] ?? 'N/A',
                        'sesiones'   => ((int)$asistidas) . ' / ' . ((int)$total),
                        'porcentaje' => $porcentaje . '%',
                        'estado'     => $row['estado'] ?? $row['estadisticas']['estado_riesgo'] ?? 'N/A',
                    ];
                })->toArray();

                // B. Definimos las columnas exactas que leerá el @foreach en Blade
                $columnas = [
                    ['key' => 'carnet',     'title' => 'Carnet'],
                    ['key' => 'estudiante', 'title' => 'Estudiante'],
                    ['key' => 'sesiones',   'title' => 'Sesiones'],
                    ['key' => 'porcentaje', 'title' => 'Porcentaje'],
                    ['key' => 'estado',     'title' => 'Estado'],
                ];

                // C. Generamos el PDF usando tu vista
                $pdf = Pdf::loadView('datatables_exports.export_pdf', [
                    'data'    => $dataParaPdf,
                    'columns' => $columnas,
                    'title'   => 'Status de Asistencia',
                    'user'    => Auth::user()->nombre_corto
                ])->setPaper('a4', 'landscape'); // 🚀 Te recomiendo landscape para que la tabla quepa bien

                return $pdf->download("Status_Asistencia.pdf");
            }

            // ==========================================
            // 🚀 2. RUTA PARA EXCEL Y CSV (MAATWEBSITE)
            // ==========================================
            $export = new StatusAsistenciaExport($datos);
            $writer = \Maatwebsite\Excel\Excel::XLSX;

            if ($format === 'csv') {
                $writer = \Maatwebsite\Excel\Excel::CSV;
                config(['excel.exports.csv.use_bom' => true]);
            }

            return $export->download("Status_Asistencia.$format", $writer);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al exportar: ' . $e->getMessage()], 500);
        }
    }


//    public function exportarStatusAsistencia(Request $request)
//    {
//        try {
//            $format = $request->query('format', 'xlsx');
//            $datos = $request->input('data', []); // Recibimos el arreglo desde el Front
//
//            if (empty($datos)) {
//                return response()->json(['message' => 'No hay datos para exportar'], 400);
//            }
//
//            $export = new StatusAsistenciaExport($datos);
//
//            $writer = \Maatwebsite\Excel\Excel::XLSX;
//
//            if ($format === 'csv') {
//                $writer = \Maatwebsite\Excel\Excel::CSV;
//                config(['excel.exports.csv.use_bom' => true]);
//            } elseif ($format === 'pdf') {
//                $writer = \Maatwebsite\Excel\Excel::DOMPDF;
//            }
//
//            return $export->download("status.$format", $writer);
//
//        } catch (\Exception $e) {
//            return response()->json(['error' => 'Error al exportar: ' . $e->getMessage()], 500);
//        }
//    }
}
