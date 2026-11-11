<?php

namespace Tests\Unit\IdentityAccess\Domain;

use App\Modules\IdentityAccess\Domain\AccessPolicy;
use App\Modules\IdentityAccess\Domain\Permission;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;

/**
 * Matriz RBAC confirmada (FUNCTIONAL_SPECIFICATIONS.md §6 y §7).
 */
class AccessPolicyTest extends TestCase
{
    public function test_administrator_has_every_permission(): void
    {
        $this->assertEqualsCanonicalizing(
            Permission::cases(),
            AccessPolicy::permissionsFor(RoleName::Administrador)
        );
    }

    public function test_director_supervises_but_does_not_operate(): void
    {
        $this->assertGrants(RoleName::Director, [
            Permission::UsersView, Permission::ReportsView, Permission::StudentsView, Permission::GradesView,
        ]);
        $this->assertDenies(RoleName::Director, [
            Permission::UsersManage, Permission::RolesManage, Permission::LapsesManage,
            Permission::StudentsManage, Permission::StudentsDelete, Permission::StudentsEnroll,
            Permission::GradesRecord,
        ]);
    }

    public function test_assistant_cannot_touch_grades_lapses_or_users(): void
    {
        $this->assertGrants(RoleName::Asistente, [Permission::StudentsEnroll, Permission::StudentsManage]);
        $this->assertDenies(RoleName::Asistente, [
            Permission::GradesRecord, Permission::GradesView, Permission::LapsesManage,
            Permission::UsersManage, Permission::UsersView,
        ]);
    }

    public function test_coordinator_sees_grades_but_not_finances_or_users(): void
    {
        $this->assertGrants(RoleName::Coordinador, [Permission::StudentsView, Permission::GradesView, Permission::LapsesManageProgram]);
        $this->assertDenies(RoleName::Coordinador, [Permission::UsersManage, Permission::ReportsView, Permission::LapsesManage]);
    }

    public function test_only_teachers_and_administrators_record_grades(): void
    {
        $graders = array_filter(
            RoleName::cases(),
            fn (RoleName $role) => in_array(Permission::GradesRecord, AccessPolicy::permissionsFor($role), true)
        );

        $this->assertEqualsCanonicalizing(
            [RoleName::Administrador, RoleName::Docente],
            array_values($graders)
        );
    }

    /** §17: solo Control de Estudios corrige notas cerradas y fuerza el cierre de actas. */
    public function test_only_administrators_override_grades_or_force_close_sheets(): void
    {
        foreach (RoleName::cases() as $role) {
            $permissions = AccessPolicy::permissionsFor($role);
            $expected = $role === RoleName::Administrador;

            $this->assertSame($expected, in_array(Permission::GradesOverride, $permissions, true), $role->value);
            $this->assertSame($expected, in_array(Permission::GradeSheetsForceClose, $permissions, true), $role->value);
        }
    }

    public function test_students_and_applicants_have_no_global_permissions(): void
    {
        $this->assertSame([], AccessPolicy::permissionsFor(RoleName::Estudiante));
        $this->assertSame([], AccessPolicy::permissionsFor(RoleName::Aspirante));
    }

    /** @param list<Permission> $permissions */
    private function assertGrants(RoleName $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->assertContains($permission, AccessPolicy::permissionsFor($role), "{$role->value} debería tener {$permission->value}");
        }
    }

    /** @param list<Permission> $permissions */
    private function assertDenies(RoleName $role, array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->assertNotContains($permission, AccessPolicy::permissionsFor($role), "{$role->value} no debería tener {$permission->value}");
        }
    }
}
