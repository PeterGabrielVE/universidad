<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class GradeSheetNotClosed extends DomainException
{
    public function __construct()
    {
        parent::__construct('El acta sigue abierta: la nota la registra el docente titular, no se corrige por auditoría.');
    }
}
