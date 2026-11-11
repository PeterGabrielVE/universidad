<?php

namespace Tests\Unit\Academic\Application;

use App\Modules\Academic\Application\Dto\ProgramInput;
use App\Modules\Academic\Application\UseCase\ManageFaculties;
use App\Modules\Academic\Application\UseCase\ManagePensum;
use App\Modules\Academic\Application\UseCase\ManagePrograms;
use App\Modules\Academic\Domain\Exception\CourseNotFound;
use App\Modules\Academic\Domain\Exception\FacultyHasPrograms;
use App\Modules\Academic\Domain\Exception\FacultyNameTaken;
use App\Modules\Academic\Domain\Exception\FacultyNotFound;
use App\Modules\Academic\Domain\Exception\ProgramInUse;
use App\Modules\Academic\Domain\Exception\ProgramNameTaken;
use App\Modules\Academic\Domain\PassingGrade;
use PHPUnit\Framework\TestCase;
use Tests\Support\Academic\InMemoryFacultyRepository;
use Tests\Support\Academic\InMemoryPensumRepository;
use Tests\Support\Academic\InMemoryProgramRepository;

/**
 * Facultades → programas (1:N), nota mínima por programa y pensum N:M (§13, §15).
 */
class ProgramUseCasesTest extends TestCase
{
    private InMemoryProgramRepository $programs;
    private InMemoryFacultyRepository $faculties;
    private ManagePrograms $managePrograms;
    private int $facultyId;

    protected function setUp(): void
    {
        $this->programs = new InMemoryProgramRepository();
        $this->faculties = new InMemoryFacultyRepository();
        $this->managePrograms = new ManagePrograms($this->programs, $this->faculties);
        $this->facultyId = $this->faculties->create('Facultad de Ciencias Económicas y Sociales', null)->id;
    }

    public function test_creates_a_program_with_its_own_minimum_grade(): void
    {
        $program = $this->managePrograms->create($this->input('Maestría en Finanzas', 60));

        $this->assertSame('Maestría en Finanzas', $program->name);
        $this->assertSame(60.0, $program->passingGrade->value());
        $this->assertSame($this->facultyId, $program->facultyId);
    }

    public function test_program_names_are_unique_regardless_of_case(): void
    {
        $this->managePrograms->create($this->input('Doctorado en Gerencia'));

        $this->expectException(ProgramNameTaken::class);
        $this->managePrograms->create($this->input('  doctorado en GERENCIA '));
    }

    public function test_a_program_can_keep_its_own_name_when_updated(): void
    {
        $program = $this->managePrograms->create($this->input('Doctorado en Gerencia', 48));

        $updated = $this->managePrograms->update($program->id, $this->input('Doctorado en Gerencia', 75));

        $this->assertSame(75.0, $updated->passingGrade->value());
    }

    public function test_program_must_belong_to_an_existing_faculty(): void
    {
        $this->expectException(FacultyNotFound::class);

        $this->managePrograms->create(new ProgramInput('Huérfano', null, 999, PassingGrade::default()));
    }

    public function test_a_program_with_users_or_lapses_cannot_be_deleted(): void
    {
        $program = $this->managePrograms->create($this->input('En uso'));
        $this->programs->inUse[$program->id] = true;

        $this->expectException(ProgramInUse::class);
        $this->managePrograms->delete($program->id);
    }

    public function test_an_unused_program_is_deleted(): void
    {
        $program = $this->managePrograms->create($this->input('Sin uso'));

        $this->managePrograms->delete($program->id);

        $this->assertNull($this->programs->find($program->id));
    }

    public function test_faculty_names_are_unique_and_faculties_with_programs_are_kept(): void
    {
        $manage = new ManageFaculties($this->faculties);

        try {
            $manage->create('facultad de ciencias económicas y sociales', null);
            $this->fail('Debió rechazar el nombre repetido');
        } catch (FacultyNameTaken) {
        }

        $this->faculties->withPrograms[$this->facultyId] = true;
        $this->expectException(FacultyHasPrograms::class);
        $manage->delete($this->facultyId);
    }

    public function test_a_transversal_course_belongs_to_several_pensums(): void
    {
        $this->programs->add(1)->add(2);
        $pensum = (new InMemoryPensumRepository())->withCourses(10);
        $manage = new ManagePensum($this->programs, $pensum);

        $manage->attach(1, 10);
        $manage->attach(2, 10);
        $manage->attach(2, 10); // idempotente

        $this->assertTrue($pensum->has(1, 10));
        $this->assertTrue($pensum->has(2, 10));

        $manage->detach(1, 10);
        $this->assertFalse($pensum->has(1, 10));
        $this->assertTrue($pensum->has(2, 10));
    }

    public function test_unknown_course_is_rejected(): void
    {
        $this->programs->add(1);
        $manage = new ManagePensum($this->programs, new InMemoryPensumRepository());

        $this->expectException(CourseNotFound::class);
        $manage->attach(1, 404);
    }

    private function input(string $name, float $minGrade = PassingGrade::DEFAULT): ProgramInput
    {
        return new ProgramInput($name, null, $this->facultyId, PassingGrade::of($minGrade));
    }
}
