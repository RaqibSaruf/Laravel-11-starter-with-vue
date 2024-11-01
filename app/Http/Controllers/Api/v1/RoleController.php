<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\UnableToDeleteException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private RoleRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('index', Role::class);

        return response()->json([
            'message' => 'Roles pagination list',
            'data' => $this->repository->paginate($request),
        ], Response::HTTP_OK);
    }

    public function list(): Response
    {
        return response()->json([
            'message' => 'ALl roles',
            'data' => $this->repository->getList(),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): Response
    {
        $this->authorize('store', Role::class);

        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => config('auth.defaults.guard'),
        ]);
        $role->syncPermissions($request->validated('permissions'));

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): Response
    {
        $this->authorize('show', $role);

        $role->load('permissions:id,name');

        return response()->json([
            'message' => 'Role retrieved successfully',
            'data' => $role,
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role): Response
    {
        $this->authorize('update', $role);

        if (!empty($request->validated('name'))) {
            $role->update(['name' => $request->validated('name')]);
        }

        if (!empty($request->validated('permissions'))) {
            $role->syncPermissions($request->validated('permissions'));
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role,
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): Response
    {
        $this->authorize('destroy', $role);

        if ($role->delete()) {
            return response()->json([
                'message' => 'Role deleted successfully',
            ], Response::HTTP_CREATED);
        }

        throw new UnableToDeleteException();
    }
}
