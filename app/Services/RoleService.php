<?php

namespace App\Services;

use App\UseCases\Role\{
    CreateRoleUseCase,
    UpdateRoleUseCase,
    DeleteRoleUseCase,
    ListRolesUseCase,
    ShowRoleUseCase
};
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function __construct(
        private CreateRoleUseCase $createUseCase,
        private UpdateRoleUseCase $updateUseCase,
        private DeleteRoleUseCase $deleteUseCase,
        private ListRolesUseCase $listUseCase,
        private ShowRoleUseCase $showUseCase
    ) {}

    public function list(): Collection
    {
        return $this->listUseCase->execute();
    }

    public function show($id): ?Role
    {
        return $this->showUseCase->execute($id);
    }

    public function create(array $data): Role
    {
        return $this->createUseCase->execute($data);
    }

    public function update($id, array $data): Role
    {
        return $this->updateUseCase->execute($id, $data);
    }

    public function delete($id): void
    {
        $this->deleteUseCase->execute($id);
    }
}
