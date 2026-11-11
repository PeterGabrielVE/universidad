<?php

namespace App\Modules\Academic\Infrastructure;

use App\Doctorado;
use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\PassingGrade;

/**
 * Los programas viven en la tabla histórica "doctorados" (ver ARCHITECTURE.md).
 */
final class EloquentProgramRepository implements ProgramRepository
{
    public function find(int $id): ?ProgramData
    {
        $program = Doctorado::find($id);

        return $program === null ? null : new ProgramData(
            $program->id,
            $program->name,
            $program->faculty_id,
            PassingGrade::of($program->min_passing_grade ?? PassingGrade::DEFAULT),
        );
    }
}
