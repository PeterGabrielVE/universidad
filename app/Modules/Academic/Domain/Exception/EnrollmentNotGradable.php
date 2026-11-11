<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class EnrollmentNotGradable extends DomainException
{
    public function __construct()
    {
        parent::__construct('Solo se registran notas de inscripciones activas de esta sección.');
    }
}
