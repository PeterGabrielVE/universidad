<?php

namespace Tests\Support\IdentityAccess;

use App\Modules\IdentityAccess\Application\Port\StudentRecords;

final class SpyStudentRecords implements StudentRecords
{
    /** @var list<int> */
    public array $ensuredFor = [];

    public function ensureFor(int $userId): void
    {
        $this->ensuredFor[] = $userId;
    }
}
