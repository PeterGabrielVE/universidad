<?php

namespace App\Modules\Academic\Infrastructure;

use App\Lapso_Estudiante;
use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Dto\EnrollmentRecord;
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

    public function find(int $id): ?EnrollmentRecord
    {
        $row = Lapso_Estudiante::find($id);

        return $row === null ? null : $this->toRecord($row);
    }

    public function inSection(int $sectionId): array
    {
        return Lapso_Estudiante::where('section_id', $sectionId)->orderBy('id')->get()
            ->map(fn (Lapso_Estudiante $row) => $this->toRecord($row))
            ->all();
    }

    public function isEnrolledInCourse(int $studentId, int $lapseId, int $courseId): bool
    {
        return Lapso_Estudiante::query()
            ->where(['student_id' => $studentId, 'lapse_id' => $lapseId, 'course_id' => $courseId])
            ->exists();
    }

    public function create(int $sectionId, int $studentId, int $lapseId, int $courseId): int
    {
        $row = new Lapso_Estudiante();
        $row->forceFill([
            'section_id' => $sectionId,
            'student_id' => $studentId,
            'lapse_id' => $lapseId,
            'course_id' => $courseId,
            'status' => EnrollmentStatus::PreInscrito,
        ])->save();

        return $row->id;
    }

    public function activate(int $id): void
    {
        Lapso_Estudiante::whereKey($id)->update(['status' => EnrollmentStatus::Activo->value]);
    }

    public function setGrade(int $id, Grade $grade): void
    {
        Lapso_Estudiante::whereKey($id)->update(['note' => $grade->value]);
    }

    public function setResult(int $id, EnrollmentResult $result): void
    {
        Lapso_Estudiante::whereKey($id)->update(['result' => $result->value]);
    }

    private function toRecord(Lapso_Estudiante $row): EnrollmentRecord
    {
        return new EnrollmentRecord(
            $row->id,
            $row->section_id,
            $row->lapse_id,
            $row->student_id,
            $row->course_id,
            $row->status ?? EnrollmentStatus::Activo,
            $row->note === null ? null : Grade::of($row->note),
            $row->result,
        );
    }
}
