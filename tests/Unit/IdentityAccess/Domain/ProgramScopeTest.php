<?php

namespace Tests\Unit\IdentityAccess\Domain;

use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;

/**
 * Programas: 1 Gerencia, 2 Ciencias, 3 ambos (FUNCTIONAL_SPECIFICATIONS.md §2).
 */
class ProgramScopeTest extends TestCase
{
    public function test_non_coordinator_staff_is_unrestricted(): void
    {
        foreach ([RoleName::Administrador, RoleName::Director, RoleName::Asistente] as $role) {
            $scope = ProgramScope::forStaff($role, 1);

            $this->assertTrue($scope->isUnrestricted());
            $this->assertTrue($scope->covers(2));
            $this->assertTrue($scope->covers(null));
        }
    }

    public function test_coordinator_of_one_program_sees_that_program_and_double_enrolled(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, ProgramScope::CIENCIAS);

        $this->assertTrue($scope->covers(ProgramScope::CIENCIAS));
        $this->assertTrue($scope->covers(ProgramScope::AMBOS));
        $this->assertFalse($scope->covers(ProgramScope::GERENCIA));
        $this->assertFalse($scope->covers(null));
    }

    public function test_coordinator_of_both_programs_sees_all_programs(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, ProgramScope::AMBOS);

        $this->assertTrue($scope->covers(ProgramScope::GERENCIA));
        $this->assertTrue($scope->covers(ProgramScope::CIENCIAS));
        $this->assertTrue($scope->covers(ProgramScope::AMBOS));
    }

    public function test_coordinator_without_program_sees_nothing(): void
    {
        $scope = ProgramScope::forStaff(RoleName::Coordinador, null);

        $this->assertSame([], $scope->programIds());
        $this->assertFalse($scope->covers(ProgramScope::GERENCIA));
    }
}
