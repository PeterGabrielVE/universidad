<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class EnrollmentNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe la inscripción {$id}.");
    }
}
