<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait BatchActionTrait
{
    private function getDelimiter()
    {
        return $this->DELIMITER ?? '::';
    }

    protected function getBatchDuplicateAvoidColumns(): array
    {
        if (method_exists($this, 'getDuplicateAvoidColumns')) {
            return $this->getDuplicateAvoidColumns();
        }

        return $this->duplicateAvoidColumns ?? [];
    }

    protected function getBatchAutoFillFields(): array
    {
        if (method_exists($this, 'getAutoFillFields')) {
            return $this->getAutoFillFields();
        }

        return $this->autoFillFields ?? [];
    }

    private function softDeleteMany(Builder $query, array $identifiers, string $identifierName = 'id'): bool
    {
        if (!in_array('deleted_at', $this->getBatchAutoFillFields(), true)) {
            throw new \RuntimeException("You don't have any softdelete auto fill field");
        }

        if (!empty($identifiers)) {
            $updatedValue = ['deleted_at' => now()];
            if (in_array('deleted_by', $this->getBatchAutoFillFields(), true)) {
                if (!Auth::check()) {
                    loginUsingBearerToken();
                }
                $userId = Auth::id() ?? 0;
                $updatedValue['deleted_by'] = $userId;
            }
            $delimiter = $this->getDelimiter();

            foreach ($this->getBatchDuplicateAvoidColumns() as $columnName) {
                $updatedValue[$columnName] = DB::raw("CONCAT($identifierName, '$delimiter', $columnName)");
            }
            $result = $query->whereIn($identifierName, $identifiers)
                ->update($updatedValue);

            return $result ? true : false;
        }

        throw ValidationException::withMessages([$identifierName => "Please provide valid $identifierName's"]);
    }

    public function scopeDeleteMany(Builder $query, array $identifiers, bool $forceDelete = false, string $identifierName = 'id'): bool
    {
        if (!$forceDelete) {
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
            if (!isset($updatedData['updated_at']) && in_array('updated_at', $this->getBatchAutoFillFields(), true)) {
                $updatedData['updated_at'] = now();
            }

            if (!isset($updatedData['updated_by']) && in_array('updated_by', $this->getBatchAutoFillFields(), true)) {
                if (!Auth::check()) {
                    loginUsingBearerToken();
                }
                $userId = Auth::id() ?? 0;
                $updatedData['updated_by'] = $userId;
            }

            $result = $query->whereIn($identifierName, $identifiers)
                ->update($updatedData);

            return $result ? true : false;
        }

        throw ValidationException::withMessages([$identifierName => "Please provide valid $identifierName's"]);
    }
}
