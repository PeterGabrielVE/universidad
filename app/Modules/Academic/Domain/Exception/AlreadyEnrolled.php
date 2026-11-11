<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class AlreadyEnrolled extends DomainException
{
    public function __construct()
    {
        parent::__construct('El estudiante ya está inscrito en esa asignatura en este lapso.');
    }

    public function field(): ?string
    {
        return 'student_id';
    }
}
