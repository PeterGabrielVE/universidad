<?php

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::all();
    }

    public function find($id): ?Role
    {
        return Role::find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update($id, array $data): Role
    {
        $role = $this->find($id);
        if (!$role) {
            throw new \Exception("Role not found.");
        }
        $role->update($data);
        return $role;
    }

    public function delete( $id): void
    {
        $role = $this->find($id);
        if (!$role) {
            throw new \Exception("Role not found.");
        }
        $role->delete();
    }
}

