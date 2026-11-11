<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class CourseNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe la asignatura {$id}.");
    }

    public function field(): ?string
    {
        return 'course_id';
    }
}
