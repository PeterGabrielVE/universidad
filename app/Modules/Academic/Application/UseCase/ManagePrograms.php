<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Dto\ProgramInput;
use App\Modules\Academic\Application\Port\FacultyRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\Exception\FacultyNotFound;
use App\Modules\Academic\Domain\Exception\ProgramInUse;
use App\Modules\Academic\Domain\Exception\ProgramNameTaken;
use App\Modules\Academic\Domain\Exception\ProgramNotFound;

/**
 * Programas de postgrado (FUNCTIONAL_SPECIFICATIONS.md §13, §15). Cambiar la nota
 * mínima solo afecta a los cierres futuros: los resultados ya cerrados están guardados.
 */
final class ManagePrograms
{
    public function __construct(
        private readonly ProgramRepository $programs,
        private readonly FacultyRepository $faculties,
    ) {
    }

    public function create(ProgramInput $input): ProgramData
    {
        $input = $this->validated($input, null);

        return $this->programs->create($input);
    }

    public function update(int $id, ProgramInput $input): ProgramData
    {
        $this->get($id);

        return $this->programs->update($id, $this->validated($input, $id));
    }

    /** Solo se elimina un programa sin usuarios ni lapsos: ambos son historia académica. */
    public function delete(int $id): void
    {
        $program = $this->get($id);

        if ($this->programs->isInUse($id)) {
            throw new ProgramInUse($program->name);
        }

        $this->programs->delete($id);
    }

    public function get(int $id): ProgramData
    {
        return $this->programs->find($id) ?? throw new ProgramNotFound($id);
    }

    private function validated(ProgramInput $input, ?int $id): ProgramInput
    {
        $name = trim($input->name);

        if ($this->programs->nameTaken($name, $id)) {
            throw new ProgramNameTaken($name);
        }

        if ($this->faculties->find($input->facultyId) === null) {
            throw new FacultyNotFound($input->facultyId);
        }

        return new ProgramInput($name, $input->description, $input->facultyId, $input->passingGrade);
    }
}
