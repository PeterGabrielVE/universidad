<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Dto\EnrollmentRecord;
use App\Modules\Academic\Application\Dto\GradeCorrection;
use App\Modules\Academic\Application\Port\AcademicPeople;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Application\Port\GradeAuditLog;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Exception\EnrollmentNotFound;
use App\Modules\Academic\Domain\Exception\EnrollmentNotGradable;
use App\Modules\Academic\Domain\Exception\GradeSheetHasUngradedStudents;
use App\Modules\Academic\Domain\Exception\GradeSheetNotClosed;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\LapseNotOpen;
use App\Modules\Academic\Domain\Exception\SectionNotFound;
use App\Modules\Academic\Domain\Grade;
use App\Modules\Academic\Domain\Justification;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\Academic\Domain\PassingGrade;
use App\Modules\Academic\Domain\Section;
use App\Modules\Shared\Application\Port\TransactionManager;

/**
 * Acta de notas de una sección (FUNCTIONAL_SPECIFICATIONS.md §15, §17):
 * 1. El docente titular registra notas mientras el acta y el lapso están abiertos.
 * 2. El docente cierra el acta (todas las inscripciones activas con nota).
 * 3. Control de Estudios puede cerrarla de forma forzosa si el docente se retrasa.
 * 4. Con el acta cerrada, solo Control de Estudios corrige, con justificación y auditoría.
 */
final class GradeSheets
{
    public function __construct(
        private readonly SectionRepository $sections,
        private readonly LapseRepository $lapses,
        private readonly EnrollmentRepository $enrollments,
        private readonly GradeAuditLog $audit,
        private readonly AcademicPeople $people,
        private readonly TransactionManager $transaction,
    ) {
    }

    /**
     * @param array<int, string|int|float|null> $grades id de inscripción => nota (vacío = sin cambios)
     */
    public function record(int $sectionId, int $teacherId, array $grades): void
    {
        [$section, $lapse] = $this->load($sectionId);
        $section->assertTaughtBy($teacherId);
        $section->assertOpen();

        if ($lapse->status() !== LapseStatus::Abierto) {
            throw new LapseNotOpen('registrar notas');
        }

        $gradable = $this->activeById($sectionId);
        $toSave = [];
        foreach ($grades as $enrollmentId => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (! isset($gradable[(int) $enrollmentId])) {
                throw new EnrollmentNotGradable();
            }
            $toSave[(int) $enrollmentId] = Grade::of($value);
        }

        $this->transaction->run(function () use ($toSave) {
            foreach ($toSave as $enrollmentId => $grade) {
                $this->enrollments->setGrade($enrollmentId, $grade);
            }
        });
    }

    public function closeByTeacher(int $sectionId, int $teacherId): Section
    {
        [$section] = $this->load($sectionId);
        $section->assertTaughtBy($teacherId);
        $section->assertOpen();

        $ungraded = array_filter($this->activeById($sectionId), fn (EnrollmentRecord $e) => $e->grade === null);
        if ($ungraded !== []) {
            throw new GradeSheetHasUngradedStudents(count($ungraded));
        }

        $section->closeByTeacher($teacherId);

        return $this->sections->save($section);
    }

    /**
     * Cierre administrativo forzoso: no exige notas completas. Las que falten las
     * completa Control de Estudios mediante correcciones auditadas.
     */
    public function forceClose(int $sectionId, int $administratorId): Section
    {
        [$section] = $this->load($sectionId);
        $section->forceClose($administratorId);

        return $this->sections->save($section);
    }

    public function correct(int $enrollmentId, string|int|float $newGrade, ?string $justification, int $administratorId): void
    {
        $enrollment = $this->enrollments->find($enrollmentId) ?? throw new EnrollmentNotFound($enrollmentId);

        if ($enrollment->sectionId === null) {
            throw new EnrollmentNotGradable();
        }

        [$section, $lapse] = $this->load($enrollment->sectionId);

        if ($section->isOpen()) {
            throw new GradeSheetNotClosed();
        }

        if (! in_array($enrollment->status, [EnrollmentStatus::Activo, EnrollmentStatus::Cerrado], true)) {
            throw new EnrollmentNotGradable();
        }

        $grade = Grade::of($newGrade);
        $reason = Justification::of($justification);

        // Lapso ya cerrado: el resultado se recalcula con la misma nota mínima del cierre.
        $newResult = $enrollment->status === EnrollmentStatus::Cerrado
            ? ($lapse->passingGradeApplied() ?? PassingGrade::default())->evaluate($grade)
            : null;

        $this->transaction->run(function () use ($enrollment, $grade, $newResult, $reason, $administratorId) {
            $this->enrollments->setGrade($enrollment->id, $grade);
            if ($newResult !== null) {
                $this->enrollments->setResult($enrollment->id, $newResult);
            }

            $this->audit->record(new GradeCorrection(
                $enrollment->id,
                $enrollment->grade,
                $grade,
                $enrollment->result,
                $newResult ?? $enrollment->result,
                $reason,
                $administratorId,
                $this->people->displayName($administratorId),
            ));
        });
    }

    /** @return array{0: Section, 1: Lapse} */
    private function load(int $sectionId): array
    {
        $section = $this->sections->find($sectionId) ?? throw new SectionNotFound($sectionId);
        $lapse = $this->lapses->find($section->lapseId()) ?? throw new LapseNotFound($section->lapseId());

        return [$section, $lapse];
    }

    /** @return array<int, EnrollmentRecord> inscripciones activas de la sección por id */
    private function activeById(int $sectionId): array
    {
        $active = [];
        foreach ($this->enrollments->inSection($sectionId) as $enrollment) {
            if ($enrollment->status === EnrollmentStatus::Activo) {
                $active[$enrollment->id] = $enrollment;
            }
        }

        return $active;
    }
}
