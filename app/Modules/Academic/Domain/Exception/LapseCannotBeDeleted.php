<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class LapseCannotBeDeleted extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("«{$name}» ya se abrió y tiene historia académica: no se puede eliminar.");
    }
}
