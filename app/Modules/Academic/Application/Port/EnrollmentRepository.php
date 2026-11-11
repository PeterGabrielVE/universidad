<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Domain\EnrollmentResult;

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
}
