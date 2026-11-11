<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Modules\IdentityAccess\Application\Port\PasswordHasher;
use Illuminate\Contracts\Hashing\Hasher;

final class LaravelPasswordHasher implements PasswordHasher
{
    public function __construct(private readonly Hasher $hasher)
    {
    }

    public function hash(string $plain): string
    {
        return $this->hasher->make($plain);
    }
}
