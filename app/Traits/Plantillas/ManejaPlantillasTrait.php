<?php

namespace App\Traits\Plantillas;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;

trait ManejaPlantillasTrait
{
    protected function guardarVersionPlantilla(
        UploadedFile $archivo,
        string $nombrePlantilla,
        ?array $variablesEnviadas,
        int $siguienteVersion,
        string $disk = 's3',
        ?int $totalPaginasEnviadas,
    ): array {

        $carpetaBase = "plantillas/{$nombrePlantilla}/{$siguienteVersion}";
        $nombreArchivo = "{$siguienteVersion}_{$nombrePlantilla}.docx";

        $contenido = $this->leerContenidoWord($archivo->getPathname());
        $totalPaginas = $this->obtenerTotalPaginasWord($archivo);
        if ($contenido === '') {
            throw ValidationException::withMessages([
                'archivo' => ['No fue posible leer el contenido del documento Word.'],
            ]);
        }

        $variablesDocumento = collect($this->extraerVariables($contenido))
            ->map(fn ($v) => strtolower(preg_replace('/\s+/', '', trim($v))))
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if ($variablesEnviadas === null) {
            $variablesFinales = $variablesDocumento;
        } else {
            $variablesEnviadas = collect($variablesEnviadas)
                ->map(fn ($v) => strtolower(preg_replace('/\s+/', '', trim($v))))
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $sobrantes = array_values(array_diff($variablesEnviadas, $variablesDocumento));
            $faltantes = array_values(array_diff($variablesDocumento, $variablesEnviadas));

            if (!empty($faltantes) || !empty($sobrantes)) {
                throw ValidationException::withMessages([
                    'variables_faltantes' => $faltantes,
                    'variables_sobrantes' => $sobrantes,
                ]);
            }

            $variablesFinales = $variablesDocumento;
        }

        $archivo->storeAs($carpetaBase, $nombreArchivo);

        return [
            'path' => 'storage/' . $carpetaBase . '/' . $nombreArchivo,
            'version' => $siguienteVersion,
            'variables' => implode(',', $variablesFinales),
            'nombre_original' => $archivo->getClientOriginalName(),
            'total_paginas' => $totalPaginas,
            'mime' => $archivo->getMimeType(),
            'size' => $archivo->getSize(),
            'disk' => $disk,
        ];
    }

    protected function leerContenidoWord(string $ruta): string
    {
        $phpWord = IOFactory::load($ruta);
        $texto = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                // Texto directo
                if (method_exists($element, 'getText')) {
                    $texto .= ' ' . $element->getText();
                }
                // Tablas
                if ($element instanceof Table) {
                    foreach ($element->getRows() as $row) {
                        foreach ($row->getCells() as $cell) {
                            foreach ($cell->getElements() as $cellElement) {

                                if (method_exists($cellElement, 'getText')) {
                                    $texto .= ' ' . $cellElement->getText();
                                }

                                // TextRun dentro de celdas
                                if ($cellElement instanceof TextRun) {
                                    foreach ($cellElement->getElements() as $textRunElement) {
                                        if (method_exists($textRunElement, 'getText')) {
                                            $texto .= ' ' . $textRunElement->getText();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($element instanceof TextRun) {
                    foreach ($element->getElements() as $textRunElement) {
                        if (method_exists($textRunElement, 'getText')) {
                            $texto .= ' ' . $textRunElement->getText();
                        }
                    }
                }
            }
        }

        return trim($texto);
    }

    protected function extraerVariables(string $contenido): array
    {
        preg_match_all('/\$\{\s*([a-zA-Z0-9_\s]+)\s*\}/', $contenido, $matches);

        return collect($matches[1])
            ->map(function ($var) {
                return strtolower(
                    preg_replace('/\s+/', '', $var) // elimina espacios
                );
            })
            ->unique()
            ->values()
            ->toArray();
    }

    public function wordPathaPdf(string $path): string
    {
        // Resolver path absoluto
        $absolutePath = str_starts_with($path, '/')
            ? $path
            : public_path($path);

        if (!file_exists($absolutePath)) {
            throw new Exception("Archivo Word no encontrado: {$absolutePath}");
        }

        // Directorio temporal
        $outDir = public_path('temp');
        if (!file_exists($outDir)) {
            mkdir($outDir, 0775, true);
        }

        $fileName = pathinfo($absolutePath, PATHINFO_FILENAME);
        $pdfPath  = $outDir . '/' . $fileName . '.pdf';

        // SI YA EXISTE → NO REGENERAR
        if (file_exists($pdfPath)) {
            return file_get_contents($pdfPath);
        }

        // Comando LibreOffice
        if (PHP_OS === 'WINNT') {
            $command = 'start /wait soffice --headless --convert-to pdf '
                . escapeshellarg($absolutePath)
                . ' --outdir ' . escapeshellarg($outDir);
        } else {
            $command = 'export HOME=/tmp && soffice --headless --convert-to pdf '
                . escapeshellarg($absolutePath)
                . ' --outdir ' . escapeshellarg($outDir);
        }

        shell_exec($command);

        if (!file_exists($pdfPath)) {
            throw new Exception('LibreOffice no generó el PDF');
        }

        return file_get_contents($pdfPath);
    }

    protected function obtenerTotalPaginasWord(UploadedFile $archivo): int
    {
        $zip = new ZipArchive();

        if ($zip->open($archivo->getPathname()) === true) {

            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if (!$xml) return 1;

            preg_match_all('/w:type="page"/', $xml, $matches);

            return count($matches[0]) + 1;
        }

        return 1;
    }

}
