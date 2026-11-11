<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Domain\StudentRecord;

interface StudentRecordRepository
{
    public function find(int $id): ?StudentRecord;

    public function save(StudentRecord $record): void;
}
