<?php

namespace Tests\Support\IdentityAccess;

use App\Modules\IdentityAccess\Application\Dto\RoleData;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use App\Modules\IdentityAccess\Domain\RoleName;

final class InMemoryRoleRepository implements RoleRepository
{
    /** @var array<int, RoleData> */
    public array $roles = [];

    private int $nextId = 1;

    public static function withSystemRoles(): self
    {
        $repository = new self();
        foreach (RoleName::cases() as $role) {
            $repository->create($role->value, $role->description());
        }

        return $repository;
    }

    public function all(): array
    {
        return array_values($this->roles);
    }

    public function findById(int $id): ?RoleData
    {
        return $this->roles[$id] ?? null;
    }

    public function findByName(string $name): ?RoleData
    {
        foreach ($this->roles as $role) {
            if ($role->name === $name) {
                return $role;
            }
        }

        return null;
    }

    public function create(string $name, ?string $description): RoleData
    {
        $id = $this->nextId++;

        return $this->roles[$id] = new RoleData($id, $name, $description);
    }

    public function update(int $id, string $name, ?string $description): RoleData
    {
        return $this->roles[$id] = new RoleData($id, $name, $description);
    }

    public function delete(int $id): void
    {
        unset($this->roles[$id]);
    }
}
