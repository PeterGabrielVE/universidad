<?php

namespace App\Modules\IdentityAccess\Application\Port;

interface PasswordHasher
{
    public function hash(string $plain): string;
}
