<?php

namespace App\Modules\Academic\Infrastructure;

use App\Asignatura;
use App\Doctorado;
use App\Models\Faculty;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use Illuminate\Support\Collection;

/**
 * Lado de lectura de facultades, programas y pensum, filtrado por alcance.
 */
final class ProgramDirectory
{
    /** @return Collection<int, Doctorado> */
    public function programs(ProgramScope $scope): Collection
    {
        return Doctorado::query()
            ->with('faculty')
            ->withCount(['users', 'lapses', 'courses'])
            ->when(! $scope->isUnrestricted(), fn ($q) => $q->whereIn('id', $scope->programIds()))
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Faculty> */
    public function faculties(): Collection
    {
        return Faculty::query()->withCount('programs')->orderBy('name')->get();
    }

    /** @return array<int, string> */
    public function facultyOptions(): array
    {
        return Faculty::orderBy('name')->pluck('name', 'id')->all();
    }

    public function program(int $id): ?Doctorado
    {
        return Doctorado::with('faculty')->find($id);
    }

    /** @return Collection<int|string, Collection<int, Asignatura>> asignaturas del pensum agrupadas por semestre */
    public function pensumBySemester(int $programId): Collection
    {
        return Asignatura::query()
            ->whereHas('programs', fn ($q) => $q->whereKey($programId))
            ->withCount('programs')
            ->orderBy('semester_id')->orderBy('cod')
            ->get()
            ->groupBy('semester_id');
    }

    /** @return array<int, string> asignaturas que aún no están en el pensum */
    public function coursesOutside(int $programId): array
    {
        return Asignatura::query()
            ->whereDoesntHave('programs', fn ($q) => $q->whereKey($programId))
            ->orderBy('semester_id')->orderBy('cod')
            ->get()
            ->mapWithKeys(fn (Asignatura $c) => [$c->id => "{$c->cod} — {$c->name} (semestre {$c->semester_id})"])
            ->all();
    }
}
