<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class FacultyHasPrograms extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("«{$name}» tiene programas: muévelos o elimínalos antes de eliminar la facultad.");
    }
}
