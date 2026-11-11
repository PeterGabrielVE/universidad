<?php

namespace App\Modules\Academic\Application\Dto;

use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\Grade;
use App\Modules\Academic\Domain\Justification;

/**
 * Registro de auditoría de una corrección de nota (grade_audit_logs).
 */
final class GradeCorrection
{
    public function __construct(
        public readonly int $enrollmentId,
        public readonly ?Grade $oldGrade,
        public readonly Grade $newGrade,
        public readonly ?EnrollmentResult $oldResult,
        public readonly ?EnrollmentResult $newResult,
        public readonly Justification $justification,
        public readonly int $changedBy,
        public readonly string $changedByName,
    ) {
    }
}
