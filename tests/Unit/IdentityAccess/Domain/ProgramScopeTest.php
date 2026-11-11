<?php

namespace Tests\Unit\IdentityAccess\Domain;

use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;

/**
 * Alcance por programa con la relación usuario ↔ programas (FUNCTIONAL_SPECIFICATIONS.md §15).
 */
class ProgramScopeTest extends TestCase
{
    public function test_non_coordinator_staff_is_unrestricted(): void
    {
        foreach ([RoleName::Administrador, RoleName::Director, RoleName::Asistente] as $role) {
            $scope = ProgramScope::forStaff($role, [1]);

            $this->assertTrue($scope->isUnrestricted());
            $this->assertTrue($scope->covers(7));
            $this->assertTrue($scope->covers(null));
            $this->assertTrue($scope->coversAny([]));
        }
    }

    public function test_coordinator_sees_only_assigned_programs(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, [2]);

        $this->assertTrue($scope->covers(2));
        $this->assertFalse($scope->covers(1));
        $this->assertFalse($scope->covers(null));
    }

    public function test_coordinator_of_several_programs_sees_all_of_them(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, [1, 2, 5]);

        $this->assertTrue($scope->covers(1));
        $this->assertTrue($scope->covers(5));
        $this->assertFalse($scope->covers(3));
    }

    /** Ya no hay comodín: el programa 3 es un programa más. */
    public function test_program_three_is_an_ordinary_program(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, [3]);

        $this->assertTrue($scope->covers(3));
        $this->assertFalse($scope->covers(1));
        $this->assertFalse($scope->covers(2));
    }

    public function test_double_degree_student_is_visible_to_either_coordinator(): void
    {
        $student = [1, 2];

        $this->assertTrue(ProgramScope::forStaff(RoleName::Coordinador, [1])->coversAny($student));
        $this->assertTrue(ProgramScope::forStaff(RoleName::Coordinador, [2])->coversAny($student));
        $this->assertFalse(ProgramScope::forStaff(RoleName::Coordinador, [4])->coversAny($student));
    }

    public function test_coordinator_without_programs_sees_nothing(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, []);

        $this->assertSame([], $scope->programIds());
        $this->assertFalse($scope->covers(1));
        $this->assertFalse($scope->coversAny([1, 2]));
    }
}
