<?php

namespace App\Modules\Academic\Infrastructure;

use App\Lapso_Estudiante;
use App\Models\Section as SectionModel;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\GradeSheetStatus;
use App\Modules\Academic\Domain\Section;

final class EloquentSectionRepository implements SectionRepository
{
    public function find(int $id): ?Section
    {
        $model = SectionModel::find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    public function save(Section $section): Section
    {
        $model = $section->id() === null ? new SectionModel() : SectionModel::findOrFail($section->id());
        $wasOpen = $model->exists ? $model->status === GradeSheetStatus::Abierta : true;

        $model->forceFill([
            'lapse_id' => $section->lapseId(),
            'course_id' => $section->courseId(),
            'code' => $section->code(),
            'teacher_id' => $section->teacherId(),
            'status' => $section->status(),
            'closed_by' => $section->closedBy(),
            'closed_forcibly' => $section->closedForcibly(),
        ]);

        if ($wasOpen && ! $section->isOpen()) {
            $model->closed_at = now();
        }

        $model->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        SectionModel::whereKey($id)->delete();
    }

    public function codeTaken(int $lapseId, int $courseId, string $code, ?int $exceptId = null): bool
    {
        return SectionModel::query()
            ->where(['lapse_id' => $lapseId, 'course_id' => $courseId, 'code' => $code])
            ->when($exceptId !== null, fn ($q) => $q->whereKeyNot($exceptId))
            ->exists();
    }

    public function hasEnrollments(int $id): bool
    {
        return Lapso_Estudiante::where('section_id', $id)->exists();
    }

    public function openCountInLapse(int $lapseId): int
    {
        return SectionModel::where('lapse_id', $lapseId)->where('status', GradeSheetStatus::Abierta->value)->count();
    }

    private function toDomain(SectionModel $model): Section
    {
        return Section::restore(
            $model->id,
            $model->lapse_id,
            $model->course_id,
            $model->code,
            $model->teacher_id,
            $model->status,
            $model->closed_by,
            (bool) $model->closed_forcibly,
        );
    }
}
