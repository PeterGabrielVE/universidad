<?php

namespace App\Modules\IdentityAccess\Application\Port;

/**
 * Expediente académico asociado a una cuenta con rol Estudiante. El resto del
 * expediente (lapso, país…) lo completa el estudiante después.
 */
interface StudentRecords
{
    public function ensureFor(int $userId): void;
}
