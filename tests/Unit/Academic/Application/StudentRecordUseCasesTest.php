<?php

namespace Tests\Unit\Academic\Application;

use App\Modules\Academic\Application\UseCase\ManageStudentRecords;
use App\Modules\Academic\Domain\ContactPhone;
use App\Modules\Academic\Domain\Exception\AdmissionLapseNotInProgram;
use App\Modules\Academic\Domain\Exception\InvalidPhone;
use App\Modules\Academic\Domain\Exception\StudentRecordOutOfScope;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\Academic\Domain\StudentRecord;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Domain\RoleName;
use PHPUnit\Framework\TestCase;
use Tests\Support\Academic\InMemoryLapseRepository;
use Tests\Support\Academic\InMemoryStudentRecordRepository;

/**
 * Expediente del estudiante (#14, FUNCTIONAL_SPECIFICATIONS.md §19).
 */
class StudentRecordUseCasesTest extends TestCase
{
    private const STUDENT = 5;
    private const OWNER = 40;

    private InMemoryStudentRecordRepository $records;
    private InMemoryLapseRepository $lapses;
    private ManageStudentRecords $manage;

    protected function setUp(): void
    {
        $this->records = (new InMemoryStudentRecordRepository())->add(
            StudentRecord::restore(self::STUDENT, self::OWNER, [1], null, null, null, false, null)
        );
        $this->lapses = new InMemoryLapseRepository();
        $this->manage = new ManageStudentRecords($this->records, $this->lapses);
    }

    public function test_phone_accepts_international_formats_and_rejects_garbage(): void
    {
        $this->assertSame('+58 414-123 4567', ContactPhone::of(' +58 414-123 4567 ')->value);

        $this->expectException(InvalidPhone::class);
        ContactPhone::of('llámame');
    }

    public function test_student_updates_own_contact(): void
    {
        $this->manage->updateOwnContact(self::STUDENT, self::OWNER, '04141234567', 3);

        $record = $this->records->find(self::STUDENT);
        $this->assertSame('04141234567', $record->phone()->value);
        $this->assertSame(3, $record->countryId());
    }

    public function test_nobody_else_uses_the_own_contact_path(): void
    {
        $this->expectException(StudentRecordOutOfScope::class);

        $this->manage->updateOwnContact(self::STUDENT, 999, '04141234567', 3);
    }

    public function test_staff_updates_administrative_data_with_a_lapse_of_the_students_program(): void
    {
        $lapse = $this->lapses->save(Lapse::plan(1, LapsePeriod::between('2026-01-12', '2026-06-26'), LapseStatus::Abierto));

        $this->manage->updateByStaff(self::STUDENT, null, 3, 2, true, $lapse->id(), ProgramScope::unrestricted());

        $record = $this->records->find(self::STUDENT);
        $this->assertSame(2, $record->campusId());
        $this->assertTrue($record->hasEquivalency());
        $this->assertSame($lapse->id(), $record->admissionLapseId());
    }

    public function test_admission_lapse_must_belong_to_one_of_the_students_programs(): void
    {
        $foreign = $this->lapses->save(Lapse::plan(2, LapsePeriod::between('2026-01-12', '2026-06-26'), LapseStatus::Abierto));

        $this->expectException(AdmissionLapseNotInProgram::class);
        $this->manage->updateByStaff(self::STUDENT, null, null, null, false, $foreign->id(), ProgramScope::unrestricted());
    }

    public function test_staff_out_of_scope_cannot_edit(): void
    {
        $this->expectException(StudentRecordOutOfScope::class);

        $this->manage->updateByStaff(self::STUDENT, null, null, null, false, null, ProgramScope::forStaff(RoleName::Coordinador, [2]));
    }
}
