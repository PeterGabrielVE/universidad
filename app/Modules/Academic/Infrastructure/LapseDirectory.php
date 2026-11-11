<?php

namespace App\Modules\Academic\Infrastructure;

use App\Doctorado;
use App\Lapso;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Lado de lectura de lapsos y programas, filtrado por el alcance del usuario.
 */
final class LapseDirectory
{
    public function paginate(ProgramScope $scope, int $perPage = 10): LengthAwarePaginator
    {
        return Lapso::query()
            ->with('program')
            ->when(! $scope->isUnrestricted(), fn ($q) => $q->whereIn('program_id', $scope->programIds()))
            ->latest('start_lapse')
            ->paginate($perPage);
    }

    /** @return array<int, string> programas en los que el usuario puede crear lapsos */
    public function programsFor(ProgramScope $scope): array
    {
        return Doctorado::query()
            ->when(! $scope->isUnrestricted(), fn ($q) => $q->whereIn('id', $scope->programIds()))
            ->orderBy('id')
            ->pluck('name', 'id')
            ->all();
    }
}
