<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

final class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): UserResource
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        return new UserResource($currentUser);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = User::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(User $user): UserResource
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        // Користувач може бачити тільки себе, якщо не є адміном
        abort_if($currentUser->id !== $user->id, 403, 'You can only view your own profile');

        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {

        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        abort_if($currentUser->id === $user->id, 403, 'You cannot delete your own account');

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
