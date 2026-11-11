<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Dto\EnrollmentRecord;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Grade;

final class InMemoryEnrollmentRepository implements EnrollmentRepository
{
    /** @var array<int, array{lapse: int, section: ?int, student: int, course: ?int, status: EnrollmentStatus, note: ?float, result: ?EnrollmentResult}> */
    public array $rows = [];

    private int $nextId = 1;

    public function add(
        int $lapseId,
        ?float $note,
        EnrollmentStatus $status = EnrollmentStatus::Activo,
        ?int $sectionId = null,
        int $studentId = 1,
        ?int $courseId = null,
    ): int {
        $id = $this->nextId++;
        $this->rows[$id] = [
            'lapse' => $lapseId, 'section' => $sectionId, 'student' => $studentId, 'course' => $courseId,
            'status' => $status, 'note' => $note, 'result' => null,
        ];

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

    public function find(int $id): ?EnrollmentRecord
    {
        return isset($this->rows[$id]) ? $this->record($id) : null;
    }

    public function inSection(int $sectionId): array
    {
        $records = [];
        foreach ($this->rows as $id => $row) {
            if ($row['section'] === $sectionId) {
                $records[] = $this->record($id);
            }
        }

        return $records;
    }

    public function isEnrolledInCourse(int $studentId, int $lapseId, int $courseId): bool
    {
        foreach ($this->rows as $row) {
            if ($row['student'] === $studentId && $row['lapse'] === $lapseId && $row['course'] === $courseId) {
                return true;
            }
        }

        return false;
    }

    public function create(int $sectionId, int $studentId, int $lapseId, int $courseId): int
    {
        return $this->add($lapseId, null, EnrollmentStatus::PreInscrito, $sectionId, $studentId, $courseId);
    }

    public function activate(int $id): void
    {
        $this->rows[$id]['status'] = EnrollmentStatus::Activo;
    }

    public function setGrade(int $id, Grade $grade): void
    {
        $this->rows[$id]['note'] = $grade->value;
    }

    public function setResult(int $id, EnrollmentResult $result): void
    {
        $this->rows[$id]['result'] = $result;
    }

    private function record(int $id): EnrollmentRecord
    {
        $row = $this->rows[$id];

        return new EnrollmentRecord(
            $id,
            $row['section'],
            $row['lapse'],
            $row['student'],
            $row['course'],
            $row['status'],
            $row['note'] === null ? null : Grade::of($row['note']),
            $row['result'],
        );
    }
}
