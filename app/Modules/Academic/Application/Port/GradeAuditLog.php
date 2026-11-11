<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\GradeCorrection;

interface GradeAuditLog
{
    public function record(GradeCorrection $correction): void;
}
