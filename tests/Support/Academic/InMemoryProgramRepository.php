<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\PassingGrade;

final class InMemoryProgramRepository implements ProgramRepository
{
    /** @var array<int, ProgramData> */
    public array $programs = [];

    public function add(int $id, float $minPassingGrade = PassingGrade::DEFAULT): self
    {
        $this->programs[$id] = new ProgramData($id, "Programa {$id}", null, PassingGrade::of($minPassingGrade));

        return $this;
    }

    public function find(int $id): ?ProgramData
    {
        return $this->programs[$id] ?? null;
    }
}
