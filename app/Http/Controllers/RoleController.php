<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private RoleService $roleService) {}

    public function index(): View
    {
        $roles = $this->roleService->list();
        return view('pages.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('pages.roles.create');
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roleService->create($request->validated());
        return redirect()->route('pages.roles.index')->with('success', 'Role created successfully.');
    }

    public function show($id): View|RedirectResponse
    {
        $role = $this->roleService->show($id);

    if (!$role) {
        return redirect()->route('roles.index')->with('error', 'Rol no encontrado');
    }

    return view('pages.roles.edit', compact('role'));
}

    public function edit($id): View|RedirectResponse
    {
        $role = $this->roleService->show($id);

    if (!$role) {
        return redirect()->route('roles.index')->with('error', 'Rol no encontrado');
    }

    return view('pages.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request,$id): RedirectResponse
    {
        $this->roleService->update($id, $request->validated());
        return redirect()->route('pages.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->roleService->delete($id);
        return redirect()->route('pages.roles.index')->with('success', 'Role deleted successfully.');
    }
}
