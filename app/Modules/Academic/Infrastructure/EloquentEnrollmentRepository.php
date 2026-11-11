<?php

namespace App\Modules\Academic\Infrastructure;

use App\Lapso_Estudiante;
use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Domain\EnrollmentResult;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\Academic\Domain\Grade;

final class EloquentEnrollmentRepository implements EnrollmentRepository
{
    public function activeInLapse(int $lapseId): array
    {
        return Lapso_Estudiante::query()
            ->where('lapse_id', $lapseId)
            ->where('status', EnrollmentStatus::Activo->value)
            ->get(['id', 'note'])
            ->map(fn (Lapso_Estudiante $row) => new ActiveEnrollment(
                $row->id,
                $row->note === null ? null : Grade::of($row->note),
            ))
            ->all();
    }

    public function close(int $enrollmentId, EnrollmentResult $result): void
    {
        Lapso_Estudiante::whereKey($enrollmentId)->update([
            'status' => EnrollmentStatus::Cerrado->value,
            'result' => $result->value,
        ]);
    }
}
