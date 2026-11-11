<?php

namespace App\Modules\IdentityAccess\Application\UseCase;

use App\Modules\IdentityAccess\Application\Dto\RoleData;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use App\Modules\IdentityAccess\Domain\Exception\RoleAlreadyExists;
use App\Modules\IdentityAccess\Domain\Exception\RoleNotFound;
use App\Modules\IdentityAccess\Domain\Exception\SystemRoleIsProtected;
use App\Modules\IdentityAccess\Domain\RoleName;

/**
 * Casos de uso de roles. Se agrupan en una clase porque comparten dependencia
 * y reglas (nombre único, roles del sistema protegidos) y cada uno es corto;
 * sustituye a RoleService + cinco UseCases que solo reenviaban llamadas.
 */
final class ManageRoles
{
    public function __construct(private readonly RoleRepository $roles)
    {
    }

    /** @return list<RoleData> */
    public function list(): array
    {
        return $this->roles->all();
    }

    public function get(int $id): RoleData
    {
        return $this->roles->findById($id) ?? throw RoleNotFound::withId($id);
    }

    public function create(string $name, ?string $description): RoleData
    {
        $name = trim($name);

        if ($this->roles->findByName($name) !== null) {
            throw new RoleAlreadyExists($name);
        }

        return $this->roles->create($name, $description);
    }

    public function update(int $id, string $name, ?string $description): RoleData
    {
        $current = $this->get($id);
        $name = trim($name);

        if ($name !== $current->name) {
            if (RoleName::isSystemRole($current->name)) {
                throw new SystemRoleIsProtected($current->name);
            }

            $existing = $this->roles->findByName($name);
            if ($existing !== null && $existing->id !== $id) {
                throw new RoleAlreadyExists($name);
            }
        }

        return $this->roles->update($id, $name, $description);
    }

    public function delete(int $id): void
    {
        $current = $this->get($id);

        if (RoleName::isSystemRole($current->name)) {
            throw new SystemRoleIsProtected($current->name);
        }

        $this->roles->delete($id);
    }
}
