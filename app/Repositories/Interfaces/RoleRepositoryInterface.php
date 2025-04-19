<?php

namespace App\Repositories\Interfaces;

use App\Models\Role;

interface RoleRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;
    public function find(int $id): ?Role;
    public function create(array $data): Role;
    public function update(int $id, array $data): Role;
    public function delete(int $id): void;
}
