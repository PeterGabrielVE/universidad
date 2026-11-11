<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\StudentRecordRepository;
use App\Modules\Academic\Domain\ContactPhone;
use App\Modules\Academic\Domain\Exception\AdmissionLapseNotInProgram;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\StudentNotFound;
use App\Modules\Academic\Domain\Exception\StudentRecordOutOfScope;
use App\Modules\Academic\Domain\StudentRecord;
use App\Modules\IdentityAccess\Domain\ProgramScope;

/**
 * Edición del expediente (#14). El estudiante solo toca sus datos de contacto;
 * el personal con students.manage, además, los datos administrativos.
 */
final class ManageStudentRecords
{
    public function __construct(
        private readonly StudentRecordRepository $records,
        private readonly LapseRepository $lapses,
    ) {
    }

    public function updateOwnContact(int $studentId, int $actorUserId, ?string $phone, ?int $countryId): StudentRecord
    {
        $record = $this->load($studentId);

        if (! $record->belongsTo($actorUserId)) {
            throw new StudentRecordOutOfScope();
        }

        $record->updateContact($this->phone($phone), $countryId);
        $this->records->save($record);

        return $record;
    }

    public function updateByStaff(
        int $studentId,
        ?string $phone,
        ?int $countryId,
        ?int $campusId,
        bool $hasEquivalency,
        ?int $admissionLapseId,
        ProgramScope $scope,
    ): StudentRecord {
        $record = $this->load($studentId);

        if (! $scope->coversAny($record->programIds())) {
            throw new StudentRecordOutOfScope();
        }

        if ($admissionLapseId !== null) {
            $lapse = $this->lapses->find($admissionLapseId) ?? throw new LapseNotFound($admissionLapseId);

            // Los lapsos anteriores a la Fase 3 no tienen programa: se aceptan para no perder datos históricos.
            if ($lapse->programId() !== null && ! in_array($lapse->programId(), $record->programIds(), true)) {
                throw new AdmissionLapseNotInProgram();
            }
        }

        $record->updateContact($this->phone($phone), $countryId);
        $record->updateAdministrative($campusId, $hasEquivalency, $admissionLapseId);
        $this->records->save($record);

        return $record;
    }

    private function load(int $studentId): StudentRecord
    {
        return $this->records->find($studentId) ?? throw new StudentNotFound($studentId);
    }

    private function phone(?string $raw): ?ContactPhone
    {
        return ($raw === null || trim($raw) === '') ? null : ContactPhone::of($raw);
    }
}
