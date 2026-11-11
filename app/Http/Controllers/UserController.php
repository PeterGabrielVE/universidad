<?php

namespace App\Http\Controllers;

use Alert;
use App\Doctorado;
use App\Http\Presenters\UserAccountPresenter;
use App\Http\Requests\UserRequest;
use App\Modules\IdentityAccess\Application\UseCase\CreateUser;
use App\Modules\IdentityAccess\Application\UseCase\DeleteUser;
use App\Modules\IdentityAccess\Application\UseCase\ManageRoles;
use App\Modules\IdentityAccess\Application\UseCase\UpdateUser;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\Exception\UserNotFound;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\RoleName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pantallas Blade de usuarios. El listado lo pinta el componente Vue
 * <users-index>, que consulta /api/v1/users; aquí solo se le pasa el filtro.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return $this->listing(null, 'Usuarios');
    }

    public function indexAdministrativo(): View
    {
        return $this->listing(RoleName::Director, 'Directivos');
    }

    public function indexDirectivo(): View
    {
        return $this->listing(RoleName::Coordinador, 'Coordinadores');
    }

    public function indexAsistente(): View
    {
        return $this->listing(RoleName::Asistente, 'Asistentes de Postgrado');
    }

    public function create(ManageRoles $roles): View
    {
        return view('pages.usuarios.create', $this->formOptions($roles));
    }

    public function store(UserRequest $request, CreateUser $createUser): RedirectResponse
    {
        $createUser->execute($request->toUserData());
        Alert::success('Usuario', '¡Creado exitosamente!');

        return back();
    }

    public function show(int $user, UserRepository $users): JsonResponse
    {
        $account = $users->find($user) ?? throw new UserNotFound($user);

        return response()->json(UserAccountPresenter::toArray($account));
    }

    public function edit(int $user, UserRepository $users, ManageRoles $roles): View
    {
        $account = $users->find($user) ?? throw new UserNotFound($user);

        return view('pages.usuarios.edit', ['user' => $account] + $this->formOptions($roles));
    }

    public function update(UserRequest $request, int $user, UpdateUser $updateUser): RedirectResponse
    {
        $updateUser->execute($user, $request->toUserData());
        Alert::success('Usuario', '¡Actualizado exitosamente!');

        return redirect()->route('user.index');
    }

    public function delete(int $id, DeleteUser $deleteUser): RedirectResponse
    {
        $deleteUser->execute($id);
        Alert::success('Usuario', '¡Eliminado exitosamente!');

        return back();
    }

    private function listing(?RoleName $role, string $title): View
    {
        return view('pages.usuarios.index', ['roleFilter' => $role?->value, 'title' => $title]);
    }

    /** @return array{roles: list<string>, programs: array<int, string>, statuses: list<string>} */
    private function formOptions(ManageRoles $roles): array
    {
        return [
            'roles' => array_map(fn ($role) => $role->name, $roles->list()),
            'programs' => Doctorado::orderBy('id')->pluck('name', 'id')->all(),
            'statuses' => AccountStatus::values(),
        ];
    }
}
