<?php

namespace Tests\Unit\IdentityAccess\Application;

use App\Modules\IdentityAccess\Application\UseCase\ManageRoles;
use App\Modules\IdentityAccess\Domain\Exception\RoleAlreadyExists;
use App\Modules\IdentityAccess\Domain\Exception\RoleNotFound;
use App\Modules\IdentityAccess\Domain\Exception\SystemRoleIsProtected;
use PHPUnit\Framework\TestCase;
use Tests\Support\IdentityAccess\InMemoryRoleRepository;

class ManageRolesTest extends TestCase
{
    private InMemoryRoleRepository $roles;
    private ManageRoles $manageRoles;

    protected function setUp(): void
    {
        $this->roles = InMemoryRoleRepository::withSystemRoles();
        $this->manageRoles = new ManageRoles($this->roles);
    }

    public function test_creates_a_custom_role_with_trimmed_name(): void
    {
        $role = $this->manageRoles->create('  Jurado externo ', 'Evaluador invitado');

        $this->assertSame('Jurado externo', $role->name);
        $this->assertFalse($role->isSystemRole());
    }

    public function test_rejects_duplicate_names(): void
    {
        $this->expectException(RoleAlreadyExists::class);

        $this->manageRoles->create('Director', null);
    }

    public function test_system_roles_cannot_be_renamed(): void
    {
        $director = $this->roles->findByName('Director');

        $this->expectException(SystemRoleIsProtected::class);
        $this->manageRoles->update($director->id, 'Rector', null);
    }

    public function test_system_role_description_can_change(): void
    {
        $director = $this->roles->findByName('Director');

        $updated = $this->manageRoles->update($director->id, 'Director', 'Dirección del postgrado');

        $this->assertSame('Dirección del postgrado', $updated->description);
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $this->expectException(SystemRoleIsProtected::class);

        $this->manageRoles->delete($this->roles->findByName('Estudiante')->id);
    }

    public function test_custom_roles_can_be_renamed_and_deleted(): void
    {
        $role = $this->manageRoles->create('Temporal', null);

        $this->manageRoles->update($role->id, 'Temporal 2', null);
        $this->manageRoles->delete($role->id);

        $this->assertNull($this->roles->findById($role->id));
    }

    public function test_renaming_to_an_existing_name_is_rejected(): void
    {
        $role = $this->manageRoles->create('Temporal', null);

        $this->expectException(RoleAlreadyExists::class);
        $this->manageRoles->update($role->id, 'Coordinador', null);
    }

    public function test_unknown_role_fails(): void
    {
        $this->expectException(RoleNotFound::class);

        $this->manageRoles->delete(999);
    }
}
