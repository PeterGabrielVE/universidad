<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Port\StudentRecordRepository;
use App\Modules\Academic\Domain\StudentRecord;

final class InMemoryStudentRecordRepository implements StudentRecordRepository
{
    /** @var array<int, StudentRecord> */
    public array $records = [];

    public function add(StudentRecord $record): self
    {
        $this->records[$record->id()] = $record;

        return $this;
    }

    public function find(int $id): ?StudentRecord
    {
        return isset($this->records[$id]) ? clone $this->records[$id] : null;
    }

    public function save(StudentRecord $record): void
    {
        $this->records[$record->id()] = clone $record;
    }
}
