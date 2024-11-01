<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait FilterTrait
{
    protected function getLikeFilterFields(): array
    {
        return $this->likeFilterFields ?? [];
    }

    public function scopeFilter(Builder $query, Request|array $queryParam = []): Builder
    {
        $filters = $queryParam instanceof Request ? $queryParam->query->all() : $queryParam;
        if ($filters) {
            $defaultFillableFields = $this->fillable;
            $table = $this->getTable();
            $likeFilterFields = $this->getLikeFilterFields();
            foreach ($filters as $field => $value) {
                if (!in_array($field, $defaultFillableFields, true)) {
                    continue;
                }

                if ($likeFilterFields && in_array($field, $likeFilterFields, true) && $value) {
                    $query->where($table . '.' . $field, 'LIKE', "%$value%");
                } elseif (is_array($value) && !empty($value)) {
                    $query->whereIn($table . '.' . $field, $value);
                } elseif (is_numeric($value) || is_bool($value) || $value) {
                    $query->where($table . '.' . $field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * This method is using for creating relational query.
     *
     * @return $query
     */
    public function scopeWithFilter(Builder $query, string $relationName, Request|array $queryParam = []): Builder
    {
        try {
            $relations = explode('.', $relationName);
            $modelName = $this->{array_shift($relations)}()->getRelated()->getMorphClass();
            $model = new $modelName();
            if (!empty($relations)) {
                foreach ($relations as $relation) {
                    $modelName = $model->{$relation}()->getRelated()->getMorphClass();
                    $model = new $modelName();
                }
            }
        } catch (\Exception $e) {
            throw new ModelNotFoundException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $filters = $queryParam instanceof Request ? $queryParam->query->all() : $queryParam;
        if ($filters) {
            $defaultFillableFields = $model->getFillable();
            $likeFilterFields = method_exists($model, 'getLikeFilterFields') ? $model->getLikeFilterFields() : [];
            $table = $model->getTable();
            foreach ($filters as $field => $value) {
                if (!in_array($field, $defaultFillableFields, true)) {
                    continue;
                }
                $query->whereHas($relationName, function ($query) use ($field, $value, $likeFilterFields, $table) {
                    if ($likeFilterFields && in_array($field, $likeFilterFields, true) && $value) {
                        $query->where($table . '.' . $field, 'LIKE', "%$value%");
                    } elseif (is_array($value) && !empty($value)) {
                        $query->whereIn($table . '.' . $field, $value);
                    } elseif (is_numeric($value) || is_bool($value) || $value) {
                        $query->where($table . '.' . $field, $value);
                    }
                });
            }
        }

        return $query;
    }

    public function scopeSort(Builder $query, Request|array|string $orderBy = 'id', string $sortDirection = 'desc'): Builder
    {
        if ($orderBy instanceof Request) {
            $request = $orderBy;
            $orderBy = $request->input('order_by', 'id');
            $sortDirection = $request->input('dir', $sortDirection);
        } elseif (is_array($orderBy)) {
            $orderBy = $filters['order_by'] ?? 'id';
            $sortDirection = $filters['dir'] ?? $sortDirection;
        }

        $query->orderBy($this->getTable() . '.' . $orderBy, $sortDirection);

        return $query;
    }

    public function scopeSearch(Builder $query, Request|array|string $searchable = ''): Builder
    {
        if ($searchable instanceof Request) {
            $searchable = $searchable->input('s', '');
        } elseif (is_array($searchable)) {
            $searchable = $searchable['s'] ?? '';
        }

        if (!empty($searchable)) {
            return $this->buildSearchQuery($query, $searchable);
        }

        return $query;
    }

    public function scopeStatus(Builder $query, int|bool|string $value, string $fieldName = 'status'): Builder
    {
        return $query->where($this->getTable() . '.' . $fieldName, $value);
    }
}
