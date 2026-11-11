<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\InvalidPhone;

/**
 * Teléfono de contacto: dígitos con prefijo internacional opcional; se aceptan
 * espacios, guiones y paréntesis como separadores y se guardan tal cual.
 */
final class ContactPhone
{
    private const PATTERN = '/^\+?[0-9 ()\-]{7,20}$/';

    private function __construct(public readonly string $value)
    {
    }

    public static function of(string $raw): self
    {
        $value = trim($raw);
        $digits = preg_replace('/\D/', '', $value);

        if (! preg_match(self::PATTERN, $value) || strlen((string) $digits) < 7) {
            throw new InvalidPhone($raw);
        }

        return new self($value);
    }
}
