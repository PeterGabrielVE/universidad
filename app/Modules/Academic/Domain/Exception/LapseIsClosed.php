<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class LapseIsClosed extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("«{$name}» está cerrado: sus notas son definitivas y no admite cambios.");
    }
}
