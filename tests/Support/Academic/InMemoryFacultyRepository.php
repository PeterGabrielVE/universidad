<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Dto\FacultyData;
use App\Modules\Academic\Application\Port\FacultyRepository;

final class InMemoryFacultyRepository implements FacultyRepository
{
    /** @var array<int, FacultyData> */
    public array $faculties = [];

    /** @var array<int, true> facultades con programas */
    public array $withPrograms = [];

    private int $nextId = 1;

    public function find(int $id): ?FacultyData
    {
        return $this->faculties[$id] ?? null;
    }

    public function nameTaken(string $name, ?int $exceptId = null): bool
    {
        foreach ($this->faculties as $id => $faculty) {
            if ($id !== $exceptId && mb_strtolower($faculty->name) === mb_strtolower($name)) {
                return true;
            }
        }

        return false;
    }

    public function create(string $name, ?string $description): FacultyData
    {
        $id = $this->nextId++;

        return $this->faculties[$id] = new FacultyData($id, $name, $description);
    }

    public function update(int $id, string $name, ?string $description): FacultyData
    {
        return $this->faculties[$id] = new FacultyData($id, $name, $description);
    }

    public function hasPrograms(int $id): bool
    {
        return isset($this->withPrograms[$id]);
    }

    public function delete(int $id): void
    {
        unset($this->faculties[$id]);
    }
}
