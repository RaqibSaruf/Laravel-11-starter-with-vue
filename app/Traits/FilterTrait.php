<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

trait FilterTrait
{
    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        if ($filters) {
            $defaultFillableFields = $this->fillable;
            $table = $this->getTable();
            foreach ($filters as $field => $value) {
                if (!in_array($field, $defaultFillableFields, true)) {
                    continue;
                }

                if ($this->likeFilterFields && in_array($field, $this->likeFilterFields, true) && $value) {
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
     * This method is using for creating relational query
     * For this method we have to create getLikeFilterFields method in relational table otherwise it throws error.
     *
     * @return $query
     */
    public function scopeWithFilter(Builder $query, string $relationName, array $filters = []): Builder
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

        if ($filters) {
            $defaultFillableFields = $model->getFillable();
            $likeFilterFields = [];
            if (method_exists($model, 'getLikeFilterFields')) {
                $likeFilterFields = $model->getLikeFilterFields();
            }

            $table = $model->getTable();

            foreach ($filters as $field => $value) {
                if (!in_array($field, $defaultFillableFields, true)) {
                    continue;
                }
                $query->whereHas($relationName, function ($query) use ($field, $value, $likeFilterFields, $table) {
                    if ($likeFilterFields && in_array($field, $likeFilterFields, true)  && $value) {
                        $query->where($table . '.' . $field, 'LIKE', "%$value%");
                    } elseif (is_array($value)  && !empty($value)) {
                        $query->whereIn($table . '.' . $field, $value);
                    } elseif (is_numeric($value) || is_bool($value) || $value) {
                        $query->where($table . '.' . $field, $value);
                    }
                });
            }
        }

        return $query;
    }

    public function scopeSort(Builder $query, array $filters = []): Builder
    {
        $query->orderBy($this->getTable() . '.' . ($filters['order_by'] ?? 'id'), $filters['dir'] ?? 'desc');

        return $query;
    }

    public function scopeSearch(Builder $query, array $filters = []): Builder
    {
        if (!empty($filters['s'])) {
            return $this->buildSearchQuery($query, $filters['s']);
        }

        return $query;
    }

    public function scopeStatus(Builder $query, int|bool|string $value, string $fieldName = 'status'): Builder
    {
        return $query->where($this->getTable() . '.' . $fieldName, $value);
    }
}
