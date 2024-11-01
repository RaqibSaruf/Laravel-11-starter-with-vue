<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;

class UserRepository implements Repository
{
    public function paginate(Request $request)
    {
        return User::with('roles:id,name')
            ->search($request)
            ->filter($request)
            ->sort($request)
            ->paginate($request->input('limit', config('common.pagi_limit')));
    }
}
