<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
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
        //dd($request->all());
        $this->roleService->create($request->validated());
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
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

    public function update(RoleRequest $request,$id):JsonResponse
    {
        try {
            $role = $this->roleService->update($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente',
                'data' => $role // Opcional: devolver el rol actualizado
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol',
                'error' => $e->getMessage() // Solo para desarrollo, en producción quitar
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->roleService->delete($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol',
                'error' => $e->getMessage() // Remover en producción
            ], 500);
        }
    }
}
