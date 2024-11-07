<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CreatedUpdatedDeletedByTrait
{
    private static $DELIMITER = '::';

    protected function getDuplicateAvoidColumns(): array
    {
        return $this->duplicateAvoidColumns ?? [];
    }

    protected function getAutoFillFields(): array
    {
        return $this->autoFillFields ?? [];
    }

    public static function bootCreatedUpdatedDeletedByTrait()
    {
        static::creating(function ($model) {
            if (!Auth::check()) {
                loginUsingBearerToken();
            }
            $userId = Auth::id() ?? 0;
            if ($userId && in_array('created_by', $model->getAutoFillFields(), true)) {
                $model->created_by = $userId;
            }
        });

        static::updating(function ($model) {
            if (!Auth::check()) {
                loginUsingBearerToken();
            }
            $userId = Auth::id() ?? 0;
            if ($userId && in_array('updated_by', $model->getAutoFillFields(), true)) {
                $model->updated_by = $userId;
            }
        });

        static::deleting(function ($model) {
            if (!Auth::check()) {
                loginUsingBearerToken();
            }
            $userId = Auth::id() ?? 0;
            foreach ($model->getDuplicateAvoidColumns() as $column) {
                $newValue = $model->id . self::$DELIMITER . $model->{$column};
                $model->{$column} = $newValue;
            }
            if ($userId && in_array('deleted_by', $model->getAutoFillFields(), true)) {
                $model->deleted_by = $userId;
            }
            $model->save();
        });
    }
}
