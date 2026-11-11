<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class InvalidJustification extends DomainException
{
    public function __construct()
    {
        parent::__construct('La corrección de una nota requiere una justificación (máximo 1000 caracteres).');
    }

    public function field(): ?string
    {
        return 'justification';
    }
}
