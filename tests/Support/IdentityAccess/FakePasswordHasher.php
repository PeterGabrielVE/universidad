<?php

namespace Tests\Support\IdentityAccess;

use App\Modules\IdentityAccess\Application\Port\PasswordHasher;

/** Hash reconocible para comprobar que se hasheó sin depender de bcrypt. */
final class FakePasswordHasher implements PasswordHasher
{
    public function hash(string $plain): string
    {
        return 'hashed:' . $plain;
    }
}
