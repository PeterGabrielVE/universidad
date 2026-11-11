<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class UserNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe el usuario {$id}.");
    }
}
