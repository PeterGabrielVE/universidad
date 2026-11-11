<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Modules\IdentityAccess\Application\UseCase\ManageRoles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct(private readonly ManageRoles $roles)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(array_map(fn ($role) => $role->toArray(), $this->roles->list()));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = $this->roles->create($request->roleName(), $request->description());

        return response()->json($role->toArray(), 201);
    }

    public function update(RoleRequest $request, int $role): JsonResponse
    {
        return response()->json($this->roles->update($role, $request->roleName(), $request->description())->toArray());
    }

    public function destroy(int $role): Response
    {
        $this->roles->delete($role);

        return response()->noContent();
    }
}
