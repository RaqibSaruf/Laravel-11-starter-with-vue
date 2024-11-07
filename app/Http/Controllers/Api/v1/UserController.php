<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\UnableToDeleteException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $this->authorize('index', User::class);

        return response()->json([
            'message' => 'Users pagination list',
            'data' => $this->repository->paginate($request),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): Response
    {
        $this->authorize('store', User::class);

        $user = new User();
        $user->fill($request->only($user->getFillable()))
            ->save();

        if ($request->has('role_id')) {
            $user->syncRoles($request->role_id);
        }

        $user->load('roles:id,name');

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): Response
    {
        $this->authorize('show', $user);

        $user->is_verified = $user->isVerified();
        $user->load('roles:id,name');

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): Response
    {
        $this->authorize('update', $user);

        $user->update($request->only($user->getFillable()));

        if ($request->has('role_id')) {
            $user->syncRoles($request->role_id);
        }

        $user->load('roles:id,name');

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        $this->authorize('destroy', $user);

        if ($user->delete()) {
            return response()->json([
                'message' => 'User deleted successfully',
            ], Response::HTTP_CREATED);
        }

        throw new UnableToDeleteException();
    }
}
