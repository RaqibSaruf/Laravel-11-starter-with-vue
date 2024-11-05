<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Enums\PermissionsEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse as Response;

class PermissionController extends Controller
{
    public function __invoke(): Response
    {
        return response()->json([
            'message' => 'All permissions',
            'data' => collect(PermissionsEnum::cases())
                ->map(function (PermissionsEnum $permission) {
                    return [
                        'label' => ucfirst(str_replace(['.', '/', '-', '_'], ' ', $permission->value)),
                        'value' => $permission->value,
                    ];
                })
                ->toArray(),
        ], Response::HTTP_OK);
    }
}
