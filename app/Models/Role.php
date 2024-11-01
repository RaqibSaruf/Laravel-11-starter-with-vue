<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use FilterTrait;

    protected $hidden = ['guard_name', 'pivot'];

    public function buildSearchQuery(Builder $query, $searchableText): Builder
    {
        return $query->where($this->getTable() . '.name', 'LIKE', '%' . $searchableText . '%');
    }
}
