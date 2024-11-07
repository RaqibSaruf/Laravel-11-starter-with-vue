<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\BatchActionTrait;
use App\Traits\CreatedUpdatedDeletedByTrait;
use App\Traits\FilterTrait;
use App\Traits\VerifyAccountTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use VerifyAccountTrait;
    use CreatedUpdatedDeletedByTrait;
    use BatchActionTrait;
    use SoftDeletes;
    use FilterTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'phone_country_code',
        'country_code',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $avoidDuplicateColumns = [
        'email',
        'phone',
    ];

    public function buildSearchQuery(Builder $query, $searchableText): Builder
    {
        return $query->where(function (Builder $query) use ($searchableText) {
            $tableName = $this->getTable();
            $query->where($tableName . '.name', 'LIKE', '%' . $searchableText . '%')
                ->orWhere($tableName . '.email', 'LIKE', '%' . $searchableText . '%')
                ->orWhereRaw("CONCAT({$tableName}.phone_country_code, '', {$tableName}phone) LIKE ?", ['%' . $searchableText . '%']);
        });
    }
}
