<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class FacultyNameTaken extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("Ya existe una facultad llamada «{$name}».");
    }

    public function field(): ?string
    {
        return 'name';
    }
}
