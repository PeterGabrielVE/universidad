<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Port\AcademicPeople;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Exception\AlreadyEnrolled;
use App\Modules\Academic\Domain\Exception\EnrollmentNotFound;
use App\Modules\Academic\Domain\Exception\EnrollmentNotPending;
use App\Modules\Academic\Domain\Exception\LapseIsClosed;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\LapseOutOfScope;
use App\Modules\Academic\Domain\Exception\SectionNotFound;
use App\Modules\Academic\Domain\Exception\StudentNotFound;
use App\Modules\Academic\Domain\Exception\StudentNotInProgram;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\Section;
use App\Modules\IdentityAccess\Domain\ProgramScope;

/**
 * Inscripción en una sección (§10): se crea "pre_inscrito" (falta validación de
 * pago/documentos) y el personal la confirma a "activo". Solo las activas se califican.
 */
final class ManageEnrollments
{
    public function __construct(
        private readonly SectionRepository $sections,
        private readonly LapseRepository $lapses,
        private readonly EnrollmentRepository $enrollments,
        private readonly AcademicPeople $people,
    ) {
    }

    public function enroll(int $sectionId, int $studentId, ProgramScope $scope): int
    {
        [$section, $lapse] = $this->load($sectionId, $scope);
        $programs = $this->people->programsOfStudent($studentId) ?? throw new StudentNotFound($studentId);

        if (! in_array($lapse->programId(), $programs, true)) {
            throw new StudentNotInProgram();
        }

        if ($this->enrollments->isEnrolledInCourse($studentId, $lapse->id(), $section->courseId())) {
            throw new AlreadyEnrolled();
        }

        return $this->enrollments->create($sectionId, $studentId, $lapse->id(), $section->courseId());
    }

    public function confirm(int $enrollmentId, ProgramScope $scope): void
    {
        $enrollment = $this->enrollments->find($enrollmentId) ?? throw new EnrollmentNotFound($enrollmentId);

        if ($enrollment->sectionId === null) {
            throw new EnrollmentNotPending();
        }

        $this->load($enrollment->sectionId, $scope);

        if ($enrollment->status !== EnrollmentStatus::PreInscrito) {
            throw new EnrollmentNotPending();
        }

        $this->enrollments->activate($enrollmentId);
    }

    /** @return array{0: Section, 1: Lapse} */
    private function load(int $sectionId, ProgramScope $scope): array
    {
        $section = $this->sections->find($sectionId) ?? throw new SectionNotFound($sectionId);
        $lapse = $this->lapses->find($section->lapseId()) ?? throw new LapseNotFound($section->lapseId());

        if (! $scope->covers($lapse->programId())) {
            throw new LapseOutOfScope();
        }

        if ($lapse->isClosed()) {
            throw new LapseIsClosed($lapse->name());
        }

        return [$section, $lapse];
    }
}
