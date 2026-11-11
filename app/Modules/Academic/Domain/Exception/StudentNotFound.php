<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class StudentNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe el estudiante {$id}.");
    }

    public function field(): ?string
    {
        return 'student_id';
    }
}
