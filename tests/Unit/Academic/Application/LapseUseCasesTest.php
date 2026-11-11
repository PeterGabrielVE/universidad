<?php

namespace Tests\Unit\Academic\Application;

use App\Modules\Academic\Application\UseCase\CloseLapse;
use App\Modules\Academic\Application\UseCase\ManageLapses;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Exception\LapseCannotBeDeleted;
use App\Modules\Academic\Domain\Exception\LapseHasUngradedEnrollments;
use App\Modules\Academic\Domain\Exception\LapseIsClosed;
use App\Modules\Academic\Domain\Exception\LapseOutOfScope;
use App\Modules\Academic\Domain\Exception\ProgramNotFound;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;
use Tests\Support\Academic\InMemoryEnrollmentRepository;
use Tests\Support\Academic\InMemoryLapseRepository;
use Tests\Support\Academic\InMemoryProgramRepository;
use Tests\Support\IdentityAccess\ImmediateTransaction;

class LapseUseCasesTest extends TestCase
{
    private const GERENCIA = 1;
    private const CIENCIAS = 2;

    private InMemoryLapseRepository $lapses;
    private InMemoryProgramRepository $programs;
    private InMemoryEnrollmentRepository $enrollments;
    private ManageLapses $manage;
    private ProgramScope $admin;

    protected function setUp(): void
    {
        $this->lapses = new InMemoryLapseRepository();
        $this->programs = (new InMemoryProgramRepository())->add(self::GERENCIA, 75)->add(self::CIENCIAS, 48);
        $this->enrollments = new InMemoryEnrollmentRepository();
        $close = new CloseLapse($this->lapses, $this->programs, $this->enrollments, new ImmediateTransaction());
        $this->manage = new ManageLapses($this->lapses, $this->programs, $close);
        $this->admin = ProgramScope::unrestricted();
    }

    public function test_closing_only_touches_active_enrollments_of_that_lapse(): void
    {
        $closing = $this->openLapse(self::CIENCIAS);
        $other = $this->openLapse(self::CIENCIAS);
        $passed = $this->enrollments->add($closing, 80);
        $failed = $this->enrollments->add($closing, 30);
        $frozen = $this->enrollments->add($closing, null, EnrollmentStatus::Congelado);
        $elsewhere = $this->enrollments->add($other, 90);

        $this->closeLapse($closing);

        $this->assertSame(EnrollmentResult::Aprobado, $this->enrollments->rows[$passed]['result']);
        $this->assertSame(EnrollmentResult::Reprobado, $this->enrollments->rows[$failed]['result']);
        $this->assertSame(EnrollmentStatus::Congelado, $this->enrollments->rows[$frozen]['status']);
        $this->assertSame(EnrollmentStatus::Activo, $this->enrollments->rows[$elsewhere]['status']);
        $this->assertNull($this->enrollments->rows[$elsewhere]['result']);
        $this->assertSame(LapseStatus::Cerrado, $this->lapses->find($closing)->status());
    }

    public function test_closing_uses_the_programs_minimum_grade(): void
    {
        $gerencia = $this->openLapse(self::GERENCIA);   // mínima 75
        $ciencias = $this->openLapse(self::CIENCIAS);   // mínima 48
        $inGerencia = $this->enrollments->add($gerencia, 60);
        $inCiencias = $this->enrollments->add($ciencias, 60);

        $this->closeLapse($gerencia);
        $this->closeLapse($ciencias);

        $this->assertSame(EnrollmentResult::Reprobado, $this->enrollments->rows[$inGerencia]['result']);
        $this->assertSame(EnrollmentResult::Aprobado, $this->enrollments->rows[$inCiencias]['result']);
    }

    public function test_cannot_close_with_ungraded_active_enrollments(): void
    {
        $lapse = $this->openLapse(self::CIENCIAS);
        $graded = $this->enrollments->add($lapse, 70);
        $this->enrollments->add($lapse, null);

        try {
            $this->closeLapse($lapse);
            $this->fail('Debió rechazar el cierre');
        } catch (LapseHasUngradedEnrollments) {
            $this->assertSame(LapseStatus::Abierto, $this->lapses->find($lapse)->status());
            $this->assertSame(EnrollmentStatus::Activo, $this->enrollments->rows[$graded]['status']);
        }
    }

    public function test_a_closed_lapse_cannot_be_edited_or_reclosed(): void
    {
        $lapse = $this->openLapse(self::CIENCIAS);
        $this->closeLapse($lapse);

        $this->expectException(LapseIsClosed::class);
        $this->manage->update($lapse, '2026-01-12', '2026-06-30', LapseStatus::Abierto, $this->admin);
    }

    public function test_legacy_lapse_without_program_uses_the_base_minimum(): void
    {
        $legacy = $this->lapses->save(Lapse::restore(99, null, LapsePeriod::between('2025-01-13', '2025-06-27'), LapseStatus::Abierto));
        $enrollment = $this->enrollments->add($legacy->id(), 48);

        $this->closeLapse($legacy->id());

        $this->assertSame(EnrollmentResult::Aprobado, $this->enrollments->rows[$enrollment]['result']);
    }

    public function test_coordinator_creates_lapses_only_in_own_program(): void
    {
        $coordinator = ProgramScope::forStaff(RoleName::Coordinador, self::CIENCIAS);

        $created = $this->manage->create(self::CIENCIAS, '2027-01-11', '2027-06-25', LapseStatus::Planificacion, $coordinator);
        $this->assertSame('Lapso Enero 2027-Junio 2027', $created->name());

        $this->expectException(LapseOutOfScope::class);
        $this->manage->create(self::GERENCIA, '2027-01-11', '2027-06-25', LapseStatus::Planificacion, $coordinator);
    }

    public function test_coordinator_cannot_touch_lapses_of_other_programs_or_legacy_ones(): void
    {
        $coordinator = ProgramScope::forStaff(RoleName::Coordinador, self::CIENCIAS);
        $gerencia = $this->openLapse(self::GERENCIA);

        $this->expectException(LapseOutOfScope::class);
        $this->manage->update($gerencia, '2026-01-12', '2026-06-26', LapseStatus::Cerrado, $coordinator);
    }

    public function test_unknown_program_is_rejected(): void
    {
        $this->expectException(ProgramNotFound::class);

        $this->manage->create(42, '2027-01-11', '2027-06-25', LapseStatus::Planificacion, $this->admin);
    }

    public function test_only_planned_lapses_are_deleted(): void
    {
        $planned = $this->manage->create(self::CIENCIAS, '2027-01-11', '2027-06-25', LapseStatus::Planificacion, $this->admin);
        $this->manage->delete($planned->id(), $this->admin);
        $this->assertNull($this->lapses->find($planned->id()));

        $open = $this->openLapse(self::CIENCIAS);
        $this->expectException(LapseCannotBeDeleted::class);
        $this->manage->delete($open, $this->admin);
    }

    private function openLapse(int $programId): int
    {
        return $this->manage->create($programId, '2026-01-12', '2026-06-26', LapseStatus::Abierto, $this->admin)->id();
    }

    private function closeLapse(int $id): void
    {
        $this->manage->update($id, '2026-01-12', '2026-06-26', LapseStatus::Cerrado, $this->admin);
    }
}
