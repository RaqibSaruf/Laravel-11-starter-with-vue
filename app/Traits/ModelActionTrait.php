<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

trait ModelActionTrait
{
    private static $DELIMITER = '::';

    protected function getDuplicateAvoidColumns(): array
    {
        return $this->duplicateAvoidColumns ?? [];
    }

    public static function bootCreatedUpdatedDeletedBy()
    {
        if (!Auth::check()) {
            loginUsingBearerToken();
        }
        $userId = Auth::id() ?? 0;

        static::creating(function ($model) use ($userId) {
            if ($userId && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = $userId;
            }
        });

        static::updating(function ($model) use ($userId) {
            if ($userId && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::deleting(function ($model) use ($userId) {
            foreach ($model->getDuplicateAvoidColumns() as $column) {
                $newValue = $model->id . self::$DELIMITER . $model->{$column};
                $model->{$column} = $newValue;
            }
            if ($userId && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = $userId;
            }
            $model->save();
        });
    }

    private function softDeleteMany(Builder $query, array $identifiers, string $identifierName = 'id'): bool
    {
        if (!empty($identifiers)) {
            if (!Auth::check()) {
                loginUsingBearerToken();
            }
            $userId = Auth::id() ?? 0;
            $updatedValue = ['deleted_at' => now()];
            if (Schema::hasColumn($this->getTable(), 'deleted_by')) {
                $updatedValue['deleted_by'] = $userId;
            }
            $delimiter = self::$DELIMITER ?? '::';
            foreach ($this->getDuplicateAvoidColumns() as $columnName) {
                $updatedValue[$columnName] = DB::raw("CONCAT($identifierName, '$delimiter', $columnName)");
            }
            $result = $query->whereIn($identifierName, $identifiers)
                ->update($updatedValue);

            return $result ? true : false;
        }

        throw ValidationException::withMessages([$identifierName => "Please provide valid $identifierName's"]);
    }

    public function scopeDeleteMany(Builder $query, array $identifiers, string $identifierName = 'id'): bool
    {
        if (Schema::hasColumn($this->getTable(), 'deleted_at')) {
            return $this->softDeleteMany($query, $identifiers, $identifierName);
        }

        if (!empty($identifiers)) {
            $result = $query->whereIn($identifierName, $identifiers)
                ->delete();

            return $result ? true : false;
        }

        throw ValidationException::withMessages([$identifierName => "Please provide valid $identifierName's"]);
    }

    public function scopeUpdateMany(Builder $query, array $updatedData, array $identifiers, string $identifierName = 'id'): bool
    {
        if (!empty($identifiers)) {
            if (!Auth::check()) {
                loginUsingBearerToken();
            }
            $userId = Auth::id() ?? 0;
            if (Schema::hasColumn($this->getTable(), 'updated_at')) {
                $updatedData['updated_at'] = now();
            }

            if (Schema::hasColumn($this->getTable(), 'updated_by')) {
                $updatedData['updated_by'] = $userId;
            }

            $result = $query->whereIn($identifierName, $identifiers)
                ->update($updatedData);

            return $result ? true : false;
        }

        throw ValidationException::withMessages([$identifierName => "Please provide valid $identifierName's"]);
    }
}
