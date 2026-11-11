<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class StudentNotInProgram extends DomainException
{
    public function __construct()
    {
        parent::__construct('El estudiante no pertenece al programa del lapso.');
    }

    public function field(): ?string
    {
        return 'student_id';
    }
}
