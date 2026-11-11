<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Estudiante;
use App\Modules\IdentityAccess\Application\Port\StudentRecords;

final class EloquentStudentRecords implements StudentRecords
{
    public function ensureFor(int $userId): void
    {
        Estudiante::firstOrCreate(['user_id' => $userId]);
    }
}
