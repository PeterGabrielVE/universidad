<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Grade;

final class InMemoryEnrollmentRepository implements EnrollmentRepository
{
    /** @var array<int, array{lapse: int, status: EnrollmentStatus, note: ?float, result: ?EnrollmentResult}> */
    public array $rows = [];

    private int $nextId = 1;

    public function add(int $lapseId, ?float $note, EnrollmentStatus $status = EnrollmentStatus::Activo): int
    {
        $id = $this->nextId++;
        $this->rows[$id] = ['lapse' => $lapseId, 'status' => $status, 'note' => $note, 'result' => null];

        return $id;
    }

    public function activeInLapse(int $lapseId): array
    {
        $active = [];
        foreach ($this->rows as $id => $row) {
            if ($row['lapse'] === $lapseId && $row['status'] === EnrollmentStatus::Activo) {
                $active[] = new ActiveEnrollment($id, $row['note'] === null ? null : Grade::of($row['note']));
            }
        }

        return $active;
    }

    public function close(int $enrollmentId, EnrollmentResult $result): void
    {
        $this->rows[$enrollmentId]['status'] = EnrollmentStatus::Cerrado;
        $this->rows[$enrollmentId]['result'] = $result;
    }
}
