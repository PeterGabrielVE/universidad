<?php

namespace App\UseCases\Role;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class CreateRoleUseCase
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): Role
    {
        return $this->repository->create($data);
    }
}
