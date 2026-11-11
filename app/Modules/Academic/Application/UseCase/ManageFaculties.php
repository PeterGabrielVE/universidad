<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Dto\FacultyData;
use App\Modules\Academic\Application\Port\FacultyRepository;
use App\Modules\Academic\Domain\Exception\FacultyHasPrograms;
use App\Modules\Academic\Domain\Exception\FacultyNameTaken;
use App\Modules\Academic\Domain\Exception\FacultyNotFound;

final class ManageFaculties
{
    public function __construct(private readonly FacultyRepository $faculties)
    {
    }

    public function create(string $name, ?string $description): FacultyData
    {
        $name = trim($name);

        if ($this->faculties->nameTaken($name)) {
            throw new FacultyNameTaken($name);
        }

        return $this->faculties->create($name, $description);
    }

    public function update(int $id, string $name, ?string $description): FacultyData
    {
        $this->get($id);
        $name = trim($name);

        if ($this->faculties->nameTaken($name, $id)) {
            throw new FacultyNameTaken($name);
        }

        return $this->faculties->update($id, $name, $description);
    }

    public function delete(int $id): void
    {
        $faculty = $this->get($id);

        if ($this->faculties->hasPrograms($id)) {
            throw new FacultyHasPrograms($faculty->name);
        }

        $this->faculties->delete($id);
    }

    public function get(int $id): FacultyData
    {
        return $this->faculties->find($id) ?? throw new FacultyNotFound($id);
    }
}
