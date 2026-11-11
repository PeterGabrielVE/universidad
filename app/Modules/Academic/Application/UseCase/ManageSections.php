<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Port\AcademicPeople;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\PensumRepository;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\Exception\CourseNotInPensum;
use App\Modules\Academic\Domain\Exception\LapseIsClosed;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\LapseOutOfScope;
use App\Modules\Academic\Domain\Exception\NotATeacher;
use App\Modules\Academic\Domain\Exception\SectionCodeTaken;
use App\Modules\Academic\Domain\Exception\SectionHasEnrollments;
use App\Modules\Academic\Domain\Exception\SectionNotFound;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\Section;
use App\Modules\IdentityAccess\Domain\ProgramScope;

/**
 * Secciones de un lapso: Administrador o Coordinador (de su programa) las abren
 * y asignan el docente titular (FUNCTIONAL_SPECIFICATIONS.md §17).
 */
final class ManageSections
{
    public function __construct(
        private readonly SectionRepository $sections,
        private readonly LapseRepository $lapses,
        private readonly PensumRepository $pensum,
        private readonly AcademicPeople $people,
    ) {
    }

    public function create(int $lapseId, int $courseId, string $code, int $teacherId, ProgramScope $scope): Section
    {
        $lapse = $this->editableLapse($lapseId, $scope);

        if ($lapse->programId() === null || ! $this->pensum->contains($lapse->programId(), $courseId)) {
            throw new CourseNotInPensum();
        }

        $this->assertTeacher($teacherId);
        $code = Section::normalizeCode($code);

        if ($this->sections->codeTaken($lapseId, $courseId, $code)) {
            throw new SectionCodeTaken($code);
        }

        return $this->sections->save(Section::open($lapseId, $courseId, $code, $teacherId));
    }

    public function assignTeacher(int $sectionId, int $teacherId, ProgramScope $scope): Section
    {
        $section = $this->load($sectionId, $scope);
        $this->assertTeacher($teacherId);
        $section->assignTeacher($teacherId);

        return $this->sections->save($section);
    }

    public function delete(int $sectionId, ProgramScope $scope): void
    {
        $section = $this->load($sectionId, $scope);

        if ($this->sections->hasEnrollments($sectionId)) {
            throw new SectionHasEnrollments($section->code());
        }

        $this->sections->delete($sectionId);
    }

    private function load(int $sectionId, ProgramScope $scope): Section
    {
        $section = $this->sections->find($sectionId) ?? throw new SectionNotFound($sectionId);
        $this->editableLapse($section->lapseId(), $scope);

        return $section;
    }

    private function editableLapse(int $lapseId, ProgramScope $scope): Lapse
    {
        $lapse = $this->lapses->find($lapseId) ?? throw new LapseNotFound($lapseId);

        if (! $scope->covers($lapse->programId())) {
            throw new LapseOutOfScope();
        }

        if ($lapse->isClosed()) {
            throw new LapseIsClosed($lapse->name());
        }

        return $lapse;
    }

    private function assertTeacher(int $userId): void
    {
        if (! $this->people->isActiveTeacher($userId)) {
            throw new NotATeacher();
        }
    }
}
