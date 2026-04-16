<?php


namespace App\Http\Controllers\Api;

use App\Exports\DataTableExport;
use App\Http\Controllers\AppBaseController;
use App\Traits\ExportableDataTableTrait;
use App\Traits\Plantillas\ManejaPlantillasTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
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

}
