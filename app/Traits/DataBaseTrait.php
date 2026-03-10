<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\BitacoraService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

trait DataBaseTrait
{
    protected function insertDB(string $table, array $set = []): array
    {
        $timestamp = now();
        $set['created_at'] = $timestamp;
        $set['updated_at'] = $timestamp;

        $id = (int) DB::table($table)->insertGetId($set);

        return ['id' => $id];
    }

    public function insertSingleDB(string $table, int $table_id = 0, array $set = []): ?stdClass
    {
        $where = $this->insertDB($table, $set);

        $record = DB::table($table)->where($where)->first();

        $this->saveBitacora('NUEVO REGISTRO', $where['id'] ?? null, $record ?? $set, $table_id);

        return $record;
    }

    public function insertMultipleDB(string $table, int $table_id = 0, array $set = []): ?Collection
    {
        if ($set === []) {
            return null;
        }

        $timestamp = now();
        $rows = [];

        foreach ($set as $row) {
            if (!is_array($row)) {
                continue;
            }

            $row['created_at'] = $timestamp;
            $row['updated_at'] = $timestamp;
            $rows[] = $row;
        }

        if ($rows === []) {
            return null;
        }

        DB::table($table)->insert($rows);

        $this->saveBitacora('NUEVO REGISTRO', null, $rows, $table_id);

        return collect($rows);
    }

    protected function updateDB(string $table, array $where = [], array $set = [], array $advancedWhere = []): void
    {
        $set['updated_at'] = now();

        $this->buildBaseQuery($table, $where, $advancedWhere)->update($set);
    }

    public function updateSingleDB(
        string $table,
        int $table_id = 0,
        array $where = [],
        array $set = [],
        string $accion = 'ACTUALIZAR REGISTRO'
    ): ?stdClass {
        $this->updateDB($table, $where, $set);

        $record = $this->buildBaseQuery($table, $where)->first();
        $id = isset($where['id']) && is_numeric($where['id']) ? (int) $where['id'] : null;

        $this->saveBitacora($accion, $id, $record ?? ['where' => $where, 'set' => $set], $table_id);

        return $record;
    }

    protected function deleteDB(string $table, array $where = [], array $advancedWhere = []): void
    {
        $payload = [
            'where' => $where,
            'advanced_where' => $advancedWhere,
        ];

        $this->buildBaseQuery($table, $where, $advancedWhere)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        $id = isset($where['id']) && is_numeric($where['id']) ? (int) $where['id'] : null;
        $this->saveBitacora('ELIMINAR REGISTRO', $id, $payload, 0);
    }

    public function softDeleteInCascade(string $column_name, int $id, Collection $cascade_tables, array $where = []): void
    {
        foreach ($cascade_tables as $tableConfig) {
            $table = null;
            $tableId = 0;
            $localWhere = $where;
            $advancedWhere = [];

            if (is_string($tableConfig)) {
                $table = $tableConfig;
            }

            if (is_array($tableConfig)) {
                $table = $tableConfig['table'] ?? null;
                $tableId = (int) ($tableConfig['table_id'] ?? 0);
                $localWhere = array_merge($localWhere, $tableConfig['where'] ?? []);
                $advancedWhere = $tableConfig['advanced_where'] ?? [];
            }

            if (!is_string($table) || $table === '') {
                continue;
            }

            $localWhere[$column_name] = $id;

            $this->deleteDB($table, $localWhere, $advancedWhere);
            $this->saveBitacora('ELIMINAR REGISTRO', $id, ['table' => $table, 'where' => $localWhere], $tableId);
        }
    }

    private function buildBaseQuery(string $table, array $where = [], array $advancedWhere = []): Builder
    {
        $query = DB::table($table);

        foreach ($where as $column => $value) {
            if (is_int($column) && is_array($value) && count($value) === 3) {
                $query->where((string) $value[0], (string) $value[1], $value[2]);
                continue;
            }

            if (is_string($column)) {
                $query->where($column, $value);
            }
        }

        $this->applyAdvancedWhere($query, $advancedWhere);

        return $query;
    }

    private function applyAdvancedWhere(Builder $query, array $advancedWhere = []): void
    {
        foreach ($advancedWhere as $condition) {
            if (!is_array($condition)) {
                continue;
            }

            if (array_keys($condition) === [0, 1, 2]) {
                $query->where((string) $condition[0], (string) $condition[1], $condition[2]);
                continue;
            }

            $column = $condition['column'] ?? null;
            $operator = strtolower((string) ($condition['operator'] ?? '='));

            if (!is_string($column) || $column === '') {
                continue;
            }

            if ($operator === 'in') {
                $query->whereIn($column, (array) ($condition['values'] ?? []));
                continue;
            }

            if ($operator === 'not in') {
                $query->whereNotIn($column, (array) ($condition['values'] ?? []));
                continue;
            }

            if ($operator === 'null') {
                $query->whereNull($column);
                continue;
            }

            if ($operator === 'not null') {
                $query->whereNotNull($column);
                continue;
            }

            if ($operator === 'between') {
                $values = (array) ($condition['values'] ?? []);
                if (count($values) === 2) {
                    $query->whereBetween($column, [$values[0], $values[1]]);
                }
                continue;
            }

            $value = $condition['value'] ?? null;
            $query->where($column, $condition['operator'] ?? '=', $value);
        }
    }

    private function saveBitacora(string $accion, ?int $id, mixed $data, int $tableId): void
    {
        app(BitacoraService::class)->save(
            $accion,
            $id,
            $this->encodeForBitacora($data),
            $tableId
        );
    }

    private function encodeForBitacora(mixed $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return is_string($json) ? $json : '{}';
    }
}
