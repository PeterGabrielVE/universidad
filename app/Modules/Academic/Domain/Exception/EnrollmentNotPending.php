<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class EnrollmentNotPending extends DomainException
{
    public function __construct()
    {
        parent::__construct('Solo se confirma una inscripción que está preinscrita.');
    }
}
