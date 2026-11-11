<?php

namespace App\Modules\Academic\Infrastructure;

use App\Asignatura;
use App\Doctorado;
use App\Modules\Academic\Application\Port\PensumRepository;

final class EloquentPensumRepository implements PensumRepository
{
    public function courseExists(int $courseId): bool
    {
        return Asignatura::whereKey($courseId)->exists();
    }

    public function contains(int $programId, int $courseId): bool
    {
        return Asignatura::whereKey($courseId)
            ->whereHas('programs', fn ($q) => $q->whereKey($programId))
            ->exists();
    }

    public function attach(int $programId, int $courseId): void
    {
        Doctorado::findOrFail($programId)->courses()->syncWithoutDetaching([$courseId]);
    }

    public function detach(int $programId, int $courseId): void
    {
        Doctorado::findOrFail($programId)->courses()->detach($courseId);
    }
}
