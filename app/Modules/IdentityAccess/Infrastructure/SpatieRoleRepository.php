<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Modules\IdentityAccess\Application\Dto\RoleData;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles sobre el modelo de Spatie (guard "web"). Sustituye al modelo propio
 * App\Models\Role, que apuntaba a la misma tabla sin pasar por la caché de Spatie.
 */
final class SpatieRoleRepository implements RoleRepository
{
    private const GUARD = 'web';

    public function __construct(private readonly PermissionRegistrar $registrar)
    {
    }

    public function all(): array
    {
        return Role::where('guard_name', self::GUARD)->orderBy('id')->get()
            ->map(fn (Role $role) => $this->toData($role))
            ->all();
    }

    public function findById(int $id): ?RoleData
    {
        $role = Role::where('guard_name', self::GUARD)->find($id);

        return $role === null ? null : $this->toData($role);
    }

    public function findByName(string $name): ?RoleData
    {
        $role = Role::where('guard_name', self::GUARD)->where('name', $name)->first();

        return $role === null ? null : $this->toData($role);
    }

    public function create(string $name, ?string $description): RoleData
    {
        $role = Role::create(['name' => $name, 'guard_name' => self::GUARD]);
        $role->forceFill(['description' => $description])->save();
        $this->registrar->forgetCachedPermissions();

        return $this->toData($role);
    }

    public function update(int $id, string $name, ?string $description): RoleData
    {
        $role = Role::findById($id, self::GUARD);
        $role->forceFill(['name' => $name, 'description' => $description])->save();
        $this->registrar->forgetCachedPermissions();

        return $this->toData($role);
    }

    public function delete(int $id): void
    {
        Role::findById($id, self::GUARD)->delete();
        $this->registrar->forgetCachedPermissions();
    }

    private function toData(Role $role): RoleData
    {
        return new RoleData($role->id, $role->name, $role->getAttribute('description'));
    }
}
