<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\Shared\Domain\DomainException;

final class EmailAlreadyInUse extends DomainException
{
    public function __construct(Email $email)
    {
        parent::__construct("Ya existe una cuenta con el correo {$email->value}.");
    }

    public function field(): ?string
    {
        return 'email';
    }
}
