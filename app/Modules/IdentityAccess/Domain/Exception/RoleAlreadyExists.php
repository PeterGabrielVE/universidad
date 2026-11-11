<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class RoleAlreadyExists extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("Ya existe un rol llamado «{$name}».");
    }

    public function field(): ?string
    {
        return 'name';
    }
}
