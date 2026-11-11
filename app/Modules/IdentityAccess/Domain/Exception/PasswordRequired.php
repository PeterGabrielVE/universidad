<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class PasswordRequired extends DomainException
{
    public function __construct()
    {
        parent::__construct('Una cuenta nueva necesita contraseña.');
    }

    public function field(): ?string
    {
        return 'password';
    }
}
