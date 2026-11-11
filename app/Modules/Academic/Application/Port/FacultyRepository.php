<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\FacultyData;

interface FacultyRepository
{
    public function find(int $id): ?FacultyData;

    /** Sin distinguir mayúsculas. */
    public function nameTaken(string $name, ?int $exceptId = null): bool;

    public function create(string $name, ?string $description): FacultyData;

    public function update(int $id, string $name, ?string $description): FacultyData;

    public function hasPrograms(int $id): bool;

    public function delete(int $id): void;
}
