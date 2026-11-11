<?php

namespace App\Modules\Academic\Application\Dto;

use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Grade;

/**
 * Inscripción de un estudiante en una sección (fila de lapses_students).
 */
final class EnrollmentRecord
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $sectionId,
        public readonly int $lapseId,
        public readonly int $studentId,
        public readonly ?int $courseId,
        public readonly EnrollmentStatus $status,
        public readonly ?Grade $grade,
        public readonly ?EnrollmentResult $result,
    ) {
    }
}
