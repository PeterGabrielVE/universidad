<?php

namespace Tests\Unit\Academic\Application;

use App\Modules\Academic\Application\UseCase\CloseLapse;
use App\Modules\Academic\Application\UseCase\GradeSheets;
use App\Modules\Academic\Application\UseCase\ManageEnrollments;
use App\Modules\Academic\Application\UseCase\ManageSections;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Exception\AlreadyEnrolled;
use App\Modules\Academic\Domain\Exception\CourseNotInPensum;
use App\Modules\Academic\Domain\Exception\EnrollmentNotGradable;
use App\Modules\Academic\Domain\Exception\GradeSheetClosed;
use App\Modules\Academic\Domain\Exception\GradeSheetHasUngradedStudents;
use App\Modules\Academic\Domain\Exception\GradeSheetNotClosed;
use App\Modules\Academic\Domain\Exception\InvalidJustification;
use App\Modules\Academic\Domain\Exception\LapseHasOpenGradeSheets;
use App\Modules\Academic\Domain\Exception\LapseOutOfScope;
use App\Modules\Academic\Domain\Exception\NotATeacher;
use App\Modules\Academic\Domain\Exception\NotSectionTeacher;
use App\Modules\Academic\Domain\Exception\SectionCodeTaken;
use App\Modules\Academic\Domain\Exception\StudentNotInProgram;
use App\Modules\Academic\Domain\GradeSheetStatus;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;
use Tests\Support\Academic\FakeAcademicPeople;
use Tests\Support\Academic\InMemoryEnrollmentRepository;
use Tests\Support\Academic\InMemoryGradeAuditLog;
use Tests\Support\Academic\InMemoryLapseRepository;
use Tests\Support\Academic\InMemoryPensumRepository;
use Tests\Support\Academic\InMemoryProgramRepository;
use Tests\Support\Academic\InMemorySectionRepository;
use Tests\Support\IdentityAccess\ImmediateTransaction;

/**
 * Lapso → Asignatura → Sección → Docente, acta y correcciones (FUNCTIONAL_SPECIFICATIONS.md §15, §17).
 */
class GradeSheetUseCasesTest extends TestCase
{
    private const PROGRAM = 1;
    private const COURSE = 10;
    private const TEACHER = 50;
    private const OTHER_TEACHER = 51;
    private const ADMIN = 90;
    private const STUDENT = 7;

    private InMemoryLapseRepository $lapses;
    private InMemoryEnrollmentRepository $enrollments;
    private InMemorySectionRepository $sections;
    private InMemoryGradeAuditLog $audit;
    private FakeAcademicPeople $people;
    private ManageSections $manageSections;
    private ManageEnrollments $manageEnrollments;
    private GradeSheets $sheets;
    private CloseLapse $closeLapse;
    private ProgramScope $admin;
    private int $lapseId;

    protected function setUp(): void
    {
        $this->lapses = new InMemoryLapseRepository();
        $this->enrollments = new InMemoryEnrollmentRepository();
        $this->sections = new InMemorySectionRepository($this->enrollments);
        $this->audit = new InMemoryGradeAuditLog();
        $this->people = new FakeAcademicPeople();
        $this->people->teachers = [self::TEACHER => true, self::OTHER_TEACHER => true];
        $this->people->studentPrograms = [self::STUDENT => [self::PROGRAM], 8 => [2]];
        $programs = (new InMemoryProgramRepository())->add(self::PROGRAM, 75);
        $pensum = (new InMemoryPensumRepository())->withCourses(self::COURSE, 11);
        $pensum->attach(self::PROGRAM, self::COURSE);
        $transaction = new ImmediateTransaction();

        $this->manageSections = new ManageSections($this->sections, $this->lapses, $pensum, $this->people);
        $this->manageEnrollments = new ManageEnrollments($this->sections, $this->lapses, $this->enrollments, $this->people);
        $this->sheets = new GradeSheets($this->sections, $this->lapses, $this->enrollments, $this->audit, $this->people, $transaction);
        $this->closeLapse = new CloseLapse($this->lapses, $programs, $this->enrollments, $this->sections, $transaction);
        $this->admin = ProgramScope::unrestricted();

        $this->lapseId = $this->lapses->save(Lapse::plan(self::PROGRAM, LapsePeriod::between('2026-07-13', '2026-12-11'), LapseStatus::Abierto))->id();
    }

    public function test_several_sections_of_a_course_each_with_one_teacher(): void
    {
        $a = $this->manageSections->create($this->lapseId, self::COURSE, ' a ', self::TEACHER, $this->admin);
        $b = $this->manageSections->create($this->lapseId, self::COURSE, 'B', self::OTHER_TEACHER, $this->admin);

        $this->assertSame('A', $a->code());
        $this->assertSame(self::OTHER_TEACHER, $b->teacherId());

        $this->expectException(SectionCodeTaken::class);
        $this->manageSections->create($this->lapseId, self::COURSE, 'a', self::TEACHER, $this->admin);
    }

    public function test_section_course_must_be_in_the_programs_pensum(): void
    {
        $this->expectException(CourseNotInPensum::class);

        $this->manageSections->create($this->lapseId, 11, 'A', self::TEACHER, $this->admin);
    }

    public function test_the_lead_teacher_must_be_a_teacher(): void
    {
        $this->expectException(NotATeacher::class);

        $this->manageSections->create($this->lapseId, self::COURSE, 'A', 999, $this->admin);
    }

    public function test_coordinator_of_another_program_cannot_open_sections(): void
    {
        $this->expectException(LapseOutOfScope::class);

        $this->manageSections->create($this->lapseId, self::COURSE, 'A', self::TEACHER, ProgramScope::forStaff(RoleName::Coordinador, [2]));
    }

    public function test_enrollment_starts_pre_enrolled_and_is_confirmed(): void
    {
        $section = $this->section();
        $id = $this->manageEnrollments->enroll($section, self::STUDENT, $this->admin);
        $this->assertSame(EnrollmentStatus::PreInscrito, $this->enrollments->find($id)->status);

        $this->manageEnrollments->confirm($id, $this->admin);
        $this->assertSame(EnrollmentStatus::Activo, $this->enrollments->find($id)->status);
    }

    public function test_student_must_belong_to_the_program_and_enroll_once_per_course(): void
    {
        $section = $this->section();

        try {
            $this->manageEnrollments->enroll($section, 8, $this->admin);
            $this->fail('Debió rechazar al estudiante de otro programa');
        } catch (StudentNotInProgram) {
        }

        $this->manageEnrollments->enroll($section, self::STUDENT, $this->admin);
        $other = $this->manageSections->create($this->lapseId, self::COURSE, 'B', self::OTHER_TEACHER, $this->admin)->id();

        $this->expectException(AlreadyEnrolled::class);
        $this->manageEnrollments->enroll($other, self::STUDENT, $this->admin);
    }

    public function test_only_the_lead_teacher_records_grades(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();

        $this->sheets->record($section, self::TEACHER, [$enrollment => '88.5']);
        $this->assertSame(88.5, $this->enrollments->rows[$enrollment]['note']);

        $this->expectException(NotSectionTeacher::class);
        $this->sheets->record($section, self::OTHER_TEACHER, [$enrollment => 10]);
    }

    public function test_pre_enrolled_students_are_not_graded(): void
    {
        $section = $this->section();
        $pending = $this->manageEnrollments->enroll($section, self::STUDENT, $this->admin);

        $this->expectException(EnrollmentNotGradable::class);
        $this->sheets->record($section, self::TEACHER, [$pending => 70]);
    }

    public function test_teacher_closes_the_sheet_only_when_every_active_student_is_graded(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();

        try {
            $this->sheets->closeByTeacher($section, self::TEACHER);
            $this->fail('Debió exigir notas completas');
        } catch (GradeSheetHasUngradedStudents) {
        }

        $this->sheets->record($section, self::TEACHER, [$enrollment => 80]);
        $closed = $this->sheets->closeByTeacher($section, self::TEACHER);

        $this->assertSame(GradeSheetStatus::Cerrada, $closed->status());
        $this->assertFalse($closed->closedForcibly());

        $this->expectException(GradeSheetClosed::class);
        $this->sheets->record($section, self::TEACHER, [$enrollment => 90]);
    }

    public function test_lapse_cannot_close_while_a_sheet_is_open(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();
        $this->sheets->record($section, self::TEACHER, [$enrollment => 80]);

        try {
            $this->closeLapse->close($this->lapses->find($this->lapseId));
            $this->fail('Debió bloquear el cierre');
        } catch (LapseHasOpenGradeSheets) {
            $this->assertSame(LapseStatus::Abierto, $this->lapses->find($this->lapseId)->status());
        }

        $this->sheets->closeByTeacher($section, self::TEACHER);
        $closed = $this->closeLapse->close($this->lapses->find($this->lapseId));

        $this->assertSame(LapseStatus::Cerrado, $closed->status());
        $this->assertSame(75.0, $closed->passingGradeApplied()->value());
        $this->assertSame(EnrollmentResult::Aprobado, $this->enrollments->rows[$enrollment]['result']);
    }

    public function test_forced_closure_unblocks_the_lapse_and_missing_grades_are_corrected_with_audit(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();

        $forced = $this->sheets->forceClose($section, self::ADMIN);
        $this->assertTrue($forced->closedForcibly());
        $this->assertSame(self::ADMIN, $forced->closedBy());

        $this->sheets->correct($enrollment, 60, 'Acta entregada en físico por el docente', self::ADMIN);
        $this->closeLapse->close($this->lapses->find($this->lapseId));

        $this->assertSame(EnrollmentResult::Reprobado, $this->enrollments->rows[$enrollment]['result']);
        $this->assertCount(1, $this->audit->entries);
        $this->assertNull($this->audit->entries[0]->oldGrade);
    }

    public function test_corrections_require_a_closed_sheet_and_a_justification(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();

        try {
            $this->sheets->correct($enrollment, 90, 'Apelación', self::ADMIN);
            $this->fail('Con el acta abierta la nota la pone el docente');
        } catch (GradeSheetNotClosed) {
        }

        $this->sheets->record($section, self::TEACHER, [$enrollment => 40]);
        $this->sheets->closeByTeacher($section, self::TEACHER);

        $this->expectException(InvalidJustification::class);
        $this->sheets->correct($enrollment, 90, '   ', self::ADMIN);
    }

    public function test_correction_after_lapse_closure_recomputes_the_result_with_the_applied_minimum(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();
        $this->sheets->record($section, self::TEACHER, [$enrollment => 70]);
        $this->sheets->closeByTeacher($section, self::TEACHER);
        $this->closeLapse->close($this->lapses->find($this->lapseId));
        $this->assertSame(EnrollmentResult::Reprobado, $this->enrollments->rows[$enrollment]['result']); // 70 < 75

        $this->sheets->correct($enrollment, 78, 'Resolución de apelación N° 123', self::ADMIN);

        $this->assertSame(78.0, $this->enrollments->rows[$enrollment]['note']);
        $this->assertSame(EnrollmentResult::Aprobado, $this->enrollments->rows[$enrollment]['result']);
        $entry = $this->audit->entries[0];
        $this->assertSame(70.0, $entry->oldGrade->value);
        $this->assertSame(EnrollmentResult::Reprobado, $entry->oldResult);
        $this->assertSame(EnrollmentResult::Aprobado, $entry->newResult);
        $this->assertSame('Resolución de apelación N° 123', $entry->justification->text);
        $this->assertSame(self::ADMIN, $entry->changedBy);
    }

    private function section(): int
    {
        return $this->manageSections->create($this->lapseId, self::COURSE, 'A', self::TEACHER, $this->admin)->id();
    }

    /** @return array{0: int, 1: int} sección e inscripción activa */
    private function sectionWithActiveStudent(): array
    {
        $section = $this->section();
        $enrollment = $this->manageEnrollments->enroll($section, self::STUDENT, $this->admin);
        $this->manageEnrollments->confirm($enrollment, $this->admin);

        return [$section, $enrollment];
    }
}
