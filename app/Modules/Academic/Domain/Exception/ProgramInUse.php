<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class ProgramInUse extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("«{$name}» tiene usuarios o lapsos asociados: no se puede eliminar.");
    }
}
