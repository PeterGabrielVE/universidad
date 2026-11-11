<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Dto\ProgramInput;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\PassingGrade;

final class InMemoryProgramRepository implements ProgramRepository
{
    /** @var array<int, ProgramData> */
    public array $programs = [];

    /** @var array<int, true> programas con usuarios o lapsos */
    public array $inUse = [];

    private int $nextId = 100;

    public function add(int $id, float $minPassingGrade = PassingGrade::DEFAULT, ?int $facultyId = null): self
    {
        $this->programs[$id] = new ProgramData($id, "Programa {$id}", $facultyId, PassingGrade::of($minPassingGrade));

        return $this;
    }

    public function find(int $id): ?ProgramData
    {
        return $this->programs[$id] ?? null;
    }

    public function nameTaken(string $name, ?int $exceptId = null): bool
    {
        foreach ($this->programs as $id => $program) {
            if ($id !== $exceptId && mb_strtolower($program->name) === mb_strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    public function create(ProgramInput $input): ProgramData
    {
        return $this->update($this->nextId++, $input);
    }

    public function update(int $id, ProgramInput $input): ProgramData
    {
        return $this->programs[$id] = new ProgramData($id, $input->name, $input->facultyId, $input->passingGrade);
    }

    public function isInUse(int $id): bool
    {
        return isset($this->inUse[$id]);
    }

    public function delete(int $id): void
    {
        unset($this->programs[$id]);
    }
}
