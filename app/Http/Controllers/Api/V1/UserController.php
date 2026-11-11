<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Presenters\UserAccountPresenter;
use App\Http\Requests\UserRequest;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Application\UseCase\CreateUser;
use App\Modules\IdentityAccess\Application\UseCase\DeleteUser;
use App\Modules\IdentityAccess\Application\UseCase\UpdateUser;
use App\Modules\IdentityAccess\Domain\Exception\UserNotFound;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /** Filtros: ?role=Coordinador&search=texto */
    public function index(Request $request, UserDirectory $directory): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['nullable', 'string', 'max:125'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json($directory->search($validated['role'] ?? null, $validated['search'] ?? null));
    }

    public function store(UserRequest $request, CreateUser $createUser): JsonResponse
    {
        $account = $createUser->execute($request->toUserData());

        return response()->json(UserAccountPresenter::toArray($account), 201);
    }

    public function show(int $user, UserRepository $users): JsonResponse
    {
        $account = $users->find($user) ?? throw new UserNotFound($user);

        return response()->json(UserAccountPresenter::toArray($account));
    }

    public function update(UserRequest $request, int $user, UpdateUser $updateUser): JsonResponse
    {
        $account = $updateUser->execute($user, $request->toUserData());

        return response()->json(UserAccountPresenter::toArray($account));
    }

    public function destroy(int $user, DeleteUser $deleteUser): Response
    {
        $deleteUser->execute($user);

        return response()->noContent();
    }
}
