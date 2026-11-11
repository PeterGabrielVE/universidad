<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class SectionHasEnrollments extends DomainException
{
    public function __construct(string $code)
    {
        parent::__construct("La sección «{$code}» tiene estudiantes inscritos: no se puede eliminar.");
    }
}
