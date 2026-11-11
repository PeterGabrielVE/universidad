<?php

namespace App\Modules\IdentityAccess\Domain;

use App\Modules\IdentityAccess\Domain\Exception\InvalidEmail;

/**
 * Correo normalizado (sin espacios, en minúsculas) para que la unicidad no
 * dependa de cómo se escribió.
 */
final class Email
{
    private function __construct(public readonly string $value)
    {
    }

    public static function from(string $raw): self
    {
        $normalized = mb_strtolower(trim($raw));

        if (filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidEmail($raw);
        }

        return new self($normalized);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
