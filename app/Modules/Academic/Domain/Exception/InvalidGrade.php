<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class InvalidGrade extends DomainException
{
    public function __construct(string $value)
    {
        parent::__construct("La nota «{$value}» no es válida: debe estar entre 0 y 100.");
    }

    public function field(): ?string
    {
        return 'note';
    }
}
