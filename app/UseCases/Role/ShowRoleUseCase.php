<?php

namespace App\UseCases\Role;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class ShowRoleUseCase
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id): ?Role
    {
        return $this->repository->find($id);
    }
}
