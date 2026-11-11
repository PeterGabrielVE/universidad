<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Modules\IdentityAccess\Application\UseCase\ManageRoles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pantalla de roles. El listado y el borrado los hace la pantalla Vue contra
 * /api/roles; el alta y la edición entran por aquí. Ambos usan ManageRoles.
 */
class RoleController extends Controller
{
    public function __construct(private readonly ManageRoles $roles)
    {
    }

    public function index(): View
    {
        $roles = array_map(fn ($role) => $role->toArray(), $this->roles->list());

        return view('pages.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('pages.roles.create');
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->roleName(), $request->description());

        return redirect()->route('roles.index');
    }

    public function show(int $role): View
    {
        return $this->edit($role);
    }

    public function edit(int $role): View
    {
        return view('pages.roles.edit', ['role' => $this->roles->get($role)]);
    }

    public function update(RoleRequest $request, int $role): JsonResponse
    {
        $updated = $this->roles->update($role, $request->roleName(), $request->description());

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente',
            'data' => $updated->toArray(),
        ]);
    }

    public function destroy(int $role): JsonResponse
    {
        $this->roles->delete($role);

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado correctamente',
        ]);
    }
}
