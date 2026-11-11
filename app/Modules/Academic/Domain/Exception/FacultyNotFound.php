<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class FacultyNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe la facultad {$id}.");
    }

    public function field(): ?string
    {
        return 'faculty_id';
    }
}
