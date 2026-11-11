<?php

namespace Tests\Unit\Academic\Domain;

use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\Exception\InvalidGrade;
use App\Modules\Academic\Domain\Exception\InvalidLapsePeriod;
use App\Modules\Academic\Domain\Exception\InvalidLapseTransition;
use App\Modules\Academic\Domain\Exception\LapseCannotBeDeleted;
use App\Modules\Academic\Domain\Exception\LapseIsClosed;
use App\Modules\Academic\Domain\Grade;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\Academic\Domain\PassingGrade;
use PHPUnit\Framework\TestCase;

class LapseAndGradesTest extends TestCase
{
    public function test_grade_equal_to_the_minimum_passes(): void
    {
        $passing = PassingGrade::of(48);

        $this->assertSame(EnrollmentResult::Aprobado, $passing->evaluate(Grade::of(48)));
        $this->assertSame(EnrollmentResult::Reprobado, $passing->evaluate(Grade::of(47.99)));
    }

    public function test_each_program_sets_its_own_minimum(): void
    {
        $doctorate = PassingGrade::of(75);

        $this->assertSame(EnrollmentResult::Reprobado, $doctorate->evaluate(Grade::of(60)));
        $this->assertSame(EnrollmentResult::Aprobado, PassingGrade::default()->evaluate(Grade::of(60)));
    }

    public function test_grades_outside_0_100_are_rejected(): void
    {
        $this->expectException(InvalidGrade::class);

        Grade::of(100.5);
    }

    public function test_lapse_name_follows_the_historical_format(): void
    {
        $this->assertSame(
            'Lapso Julio 2026-Diciembre 2026',
            LapsePeriod::between('2026-07-13', '2026-12-11')->name()
        );
    }

    public function test_end_must_be_after_start(): void
    {
        $this->expectException(InvalidLapsePeriod::class);

        LapsePeriod::between('2026-07-13', '2026-07-13');
    }

    public function test_impossible_dates_are_rejected(): void
    {
        $this->expectException(InvalidLapsePeriod::class);

        LapsePeriod::between('2026-02-30', '2026-06-30');
    }

    public function test_status_cycle_is_planning_open_closed(): void
    {
        $lapse = $this->lapse(LapseStatus::Planificacion);

        $lapse->changeStatus(LapseStatus::Abierto);
        $lapse->close();

        $this->assertSame(LapseStatus::Cerrado, $lapse->status());
    }

    public function test_a_planned_lapse_cannot_be_closed_without_opening(): void
    {
        $this->expectException(InvalidLapseTransition::class);

        $this->lapse(LapseStatus::Planificacion)->close();
    }

    public function test_an_open_lapse_cannot_go_back_to_planning(): void
    {
        $this->expectException(InvalidLapseTransition::class);

        $this->lapse(LapseStatus::Abierto)->changeStatus(LapseStatus::Planificacion);
    }

    public function test_closing_through_change_status_is_not_allowed(): void
    {
        $this->expectException(InvalidLapseTransition::class);

        $this->lapse(LapseStatus::Abierto)->changeStatus(LapseStatus::Cerrado);
    }

    public function test_a_closed_lapse_is_immutable(): void
    {
        $lapse = $this->lapse(LapseStatus::Cerrado);

        $this->expectException(LapseIsClosed::class);
        $lapse->reschedule(LapsePeriod::between('2026-01-01', '2026-06-30'));
    }

    public function test_a_new_lapse_cannot_start_closed(): void
    {
        $this->expectException(InvalidLapseTransition::class);

        Lapse::plan(1, LapsePeriod::between('2026-01-12', '2026-06-26'), LapseStatus::Cerrado);
    }

    public function test_only_planned_lapses_can_be_deleted(): void
    {
        $this->lapse(LapseStatus::Planificacion)->assertDeletable();

        $this->expectException(LapseCannotBeDeleted::class);
        $this->lapse(LapseStatus::Abierto)->assertDeletable();
    }

    private function lapse(LapseStatus $status): Lapse
    {
        return Lapse::restore(1, 1, LapsePeriod::between('2026-01-12', '2026-06-26'), $status);
    }
}
