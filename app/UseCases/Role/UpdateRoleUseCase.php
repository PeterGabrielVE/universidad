<?php

namespace App\UseCases\Role;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class UpdateRoleUseCase
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, array $data): Role
    {
        return $this->repository->update($id, $data);
    }
}
