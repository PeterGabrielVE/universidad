<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class ProgramNameTaken extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("Ya existe un programa llamado «{$name}».");
    }

    public function field(): ?string
    {
        return 'name';
    }
}
