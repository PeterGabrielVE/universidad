<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class InvalidEmail extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("El correo «{$email}» no es válido.");
    }

    public function field(): ?string
    {
        return 'email';
    }
}
