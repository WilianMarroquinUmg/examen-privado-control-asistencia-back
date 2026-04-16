<?php

namespace App\Traits;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait ExportableDataTableTrait
{
    /**
     * Se encarga de preparar el Query Builder con relaciones y filtros dinámicos.
     * @param string $rawModel Nombre de la clase del modelo.
     * @param array $filters Filtros a aplicar.
     * @param array $columns Columnas para detectar relaciones (Eager Loading).
     * @return Builder
     */
    public function construirQuery(string $rawModel, array $filters = [], array $columns = []): Builder
    {
        $modelClass = preg_replace('/\\\\+/', '\\', $rawModel);

        if (!class_exists($modelClass)) {
            throw new Exception("Model $modelClass not found", 405);
        }

        $query = $modelClass::query();

        // 1. Eager Loading (Carga ambiciosa) de relaciones
        $relationsToLoad = collect($columns)
            ->filter(fn($col) => Str::contains($col['key'], '.'))
            ->map(fn($col) => Str::beforeLast($col['key'], '.'))
            ->unique()
            ->toArray();

        if (!empty($relationsToLoad)) {
            $query->with($relationsToLoad);
        }

        // 2. Aplicación de Filtros
        foreach ($filters as $key => $filterData) {
            if (!is_array($filterData)) {
                $filterData = ['value' => $filterData, 'operador' => 'like'];
            }

            $value = $filterData['value'] ?? null;
            $operator = $filterData['operador'] ?? 'like';

            if (is_null($value) && $operator !== 'nullable') continue;

            // Lógica de filtrado con soporte para relaciones
            if (Str::contains($key, '.')) {
                $relationPath = Str::beforeLast($key, '.');
                $columnName = Str::afterLast($key, '.');

                $query->whereHas($relationPath, function ($q) use ($columnName, $value, $operator) {
                    $this->applyQueryOperator($q, $columnName, $value, $operator);
                });
            } else {
                $this->applyQueryOperator($query, $key, $value, $operator);
            }
        }

        return $query;
    }

    /**
     * Aplica el operador específico al query.
     */
    private function applyQueryOperator($q, $column, $val, $op): void
    {
        switch ($op) {
            case 'between':     $q->whereBetween($column, $val); break;
            case 'not_between': $q->whereNotBetween($column, $val); break;
            case 'in':          $q->whereIn($column, (array)$val); break;
            case 'not_in':      $q->whereNotIn($column, (array)$val); break;
            case 'null':        $q->whereNull($column); break;
            case 'not_null':    $q->whereNotNull($column); break;
            case 'date':        $q->whereDate($column, $val); break;
            case 'like':        $q->where($column, 'like', "%$val%"); break;
            case 'scope':
                if (is_array($val)) {
                    $q->{$column}(...$val);
                } else {
                    $q->{$column}($val);
                }
                break;
            default:            $q->where($column, $op, $val); break;
        }
    }

    public function getColumnasExportables($columnas): array {
        return  array_filter($columnas, function ($col) {
            return $col['exportable'] ?? true;
        });
    }
}
