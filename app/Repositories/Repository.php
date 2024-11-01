<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Http\Request;

interface Repository
{
    public function paginate(Request $request);
}
