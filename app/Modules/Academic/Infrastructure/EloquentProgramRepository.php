<?php

namespace App\Modules\Academic\Infrastructure;

use App\Doctorado;
use App\Lapso;
use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Dto\ProgramInput;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\PassingGrade;
use Illuminate\Support\Facades\DB;

/**
 * Los programas viven en la tabla histórica "doctorados" (ver ARCHITECTURE.md).
 */
final class EloquentProgramRepository implements ProgramRepository
{
    public function find(int $id): ?ProgramData
    {
        $program = Doctorado::find($id);

        return $program === null ? null : $this->toData($program);
    }

    public function nameTaken(string $name, ?int $exceptId = null): bool
    {
        return Doctorado::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->when($exceptId !== null, fn ($q) => $q->whereKeyNot($exceptId))
            ->exists();
    }

    public function create(ProgramInput $input): ProgramData
    {
        $program = new Doctorado();
        $this->fill($program, $input);

        return $this->toData($program);
    }

    public function update(int $id, ProgramInput $input): ProgramData
    {
        $program = Doctorado::findOrFail($id);
        $this->fill($program, $input);

        return $this->toData($program);
    }

    public function isInUse(int $id): bool
    {
        return DB::table('program_user')->where('program_id', $id)->exists()
            || Lapso::where('program_id', $id)->exists();
    }

    public function delete(int $id): void
    {
        Doctorado::whereKey($id)->delete();
    }

    private function fill(Doctorado $program, ProgramInput $input): void
    {
        $program->forceFill([
            'name' => $input->name,
            'description' => $input->description,
            'faculty_id' => $input->facultyId,
            'min_passing_grade' => $input->passingGrade->value(),
        ])->save();
    }

    private function toData(Doctorado $program): ProgramData
    {
        return new ProgramData(
            $program->id,
            $program->name,
            $program->faculty_id,
            PassingGrade::of($program->min_passing_grade ?? PassingGrade::DEFAULT),
        );
    }
}
