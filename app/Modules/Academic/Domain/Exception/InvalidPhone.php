<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class InvalidPhone extends DomainException
{
    public function __construct(string $raw)
    {
        parent::__construct("El teléfono «{$raw}» no es válido: usa dígitos, con prefijo internacional opcional (p. ej. +58 414 1234567).");
    }

    public function field(): ?string
    {
        return 'phone';
    }
}
