<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class SectionCodeTaken extends DomainException
{
    public function __construct(string $code)
    {
        parent::__construct("Ya existe la sección «{$code}» de esa asignatura en este lapso.");
    }

    public function field(): ?string
    {
        return 'code';
    }
}
