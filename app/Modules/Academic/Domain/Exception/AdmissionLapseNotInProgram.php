<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class AdmissionLapseNotInProgram extends DomainException
{
    public function __construct()
    {
        parent::__construct('El lapso de ingreso debe pertenecer a uno de los programas del estudiante.');
    }

    public function field(): ?string
    {
        return 'admission_lapse_id';
    }
}
