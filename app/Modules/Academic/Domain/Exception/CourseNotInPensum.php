<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class CourseNotInPensum extends DomainException
{
    public function __construct()
    {
        parent::__construct('La asignatura no pertenece al pensum del programa del lapso.');
    }

    public function field(): ?string
    {
        return 'course_id';
    }
}
