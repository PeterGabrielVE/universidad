<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\ProgramData;
use App\Modules\Academic\Application\Dto\ProgramInput;

interface ProgramRepository
{
    public function find(int $id): ?ProgramData;

    /** Sin distinguir mayúsculas. */
    public function nameTaken(string $name, ?int $exceptId = null): bool;

    public function create(ProgramInput $input): ProgramData;

    public function update(int $id, ProgramInput $input): ProgramData;

    /** ¿Tiene usuarios (program_user) o lapsos? El pensum no cuenta: se borra con el programa. */
    public function isInUse(int $id): bool;

    public function delete(int $id): void;
}
