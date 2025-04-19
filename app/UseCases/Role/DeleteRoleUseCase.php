<?php

namespace App\UseCases\Role;

use App\Repositories\Interfaces\RoleRepositoryInterface;

class DeleteRoleUseCase
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id): void
    {
        $this->repository->delete($id);
    }
}
