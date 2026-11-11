<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Dto\EnrollmentRecord;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\Grade;

interface EnrollmentRepository
{
    /**
     * Inscripciones en estado "activo" de ese lapso y solo de ese lapso.
     *
     * @return list<ActiveEnrollment>
     */
    public function activeInLapse(int $lapseId): array;

    /** Pasa la inscripción a "cerrado" con su resultado definitivo. */
    public function close(int $enrollmentId, EnrollmentResult $result): void;

    public function find(int $id): ?EnrollmentRecord;

    /** @return list<EnrollmentRecord> */
    public function inSection(int $sectionId): array;

    public function isEnrolledInCourse(int $studentId, int $lapseId, int $courseId): bool;

    /** Crea la inscripción en estado pre_inscrito; devuelve su id. */
    public function create(int $sectionId, int $studentId, int $lapseId, int $courseId): int;

    public function activate(int $id): void;

    public function setGrade(int $id, Grade $grade): void;

    public function setResult(int $id, EnrollmentResult $result): void;
}
