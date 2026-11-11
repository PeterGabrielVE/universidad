<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\GradeCorrection;
use App\Modules\Academic\Application\Port\GradeAuditLog;

final class InMemoryGradeAuditLog implements GradeAuditLog
{
    /** @var list<GradeCorrection> */
    public array $entries = [];

    public function record(GradeCorrection $correction): void
    {
        $this->entries[] = $correction;
    }
}
