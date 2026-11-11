<?php

namespace App\Modules\Academic\Infrastructure;

use App\Estudiante;
use App\Modules\Academic\Application\Port\StudentRecordRepository;
use App\Modules\Academic\Domain\ContactPhone;
use App\Modules\Academic\Domain\StudentRecord;
use Illuminate\Support\Facades\DB;

/**
 * El expediente vive repartido: contacto y sede en users, equivalencia y lapso de
 * ingreso en students. El país se guarda en users.country_id (el que muestran los
 * listados); students.country_id se mantiene igual para no desalinearlos.
 */
final class EloquentStudentRecordRepository implements StudentRecordRepository
{
    private const EQUIVALENCY_YES = 'Sí';
    private const EQUIVALENCY_NO = 'No';

    public function find(int $id): ?StudentRecord
    {
        $student = Estudiante::with('user.programs')->find($id);

        if ($student === null || $student->user === null) {
            return null;
        }

        $user = $student->user;

        return StudentRecord::restore(
            $student->id,
            $user->id,
            $user->programs->pluck('id')->all(),
            ($user->phone === null || $user->phone === '') ? null : $this->tolerantPhone($user->phone),
            $user->country_id !== null ? (int) $user->country_id : null,
            $user->sede_id !== null ? (int) $user->sede_id : null,
            $student->equivalency === self::EQUIVALENCY_YES,
            $student->lapse_id !== null ? (int) $student->lapse_id : null,
        );
    }

    public function save(StudentRecord $record): void
    {
        DB::transaction(function () use ($record) {
            $student = Estudiante::with('user')->findOrFail($record->id());

            $student->user->forceFill([
                'phone' => $record->phone()?->value,
                'country_id' => $record->countryId(),
                'sede_id' => $record->campusId(),
            ])->save();

            $student->forceFill([
                'country_id' => $record->countryId(),
                'equivalency' => $record->hasEquivalency() ? self::EQUIVALENCY_YES : self::EQUIVALENCY_NO,
                'lapse_id' => $record->admissionLapseId(),
            ])->save();
        });
    }

    /** Teléfonos históricos que no cumplen el formato se conservan sin validar al leer. */
    private function tolerantPhone(string $phone): ?ContactPhone
    {
        try {
            return ContactPhone::of($phone);
        } catch (\App\Modules\Academic\Domain\Exception\InvalidPhone) {
            return null;
        }
    }
}
