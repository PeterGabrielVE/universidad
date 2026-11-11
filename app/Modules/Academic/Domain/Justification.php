<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\InvalidJustification;

/**
 * Respaldo obligatorio de una corrección de nota (p. ej. "Error de transcripción",
 * "Resolución de apelación N° 123"), FUNCTIONAL_SPECIFICATIONS.md §17.
 */
final class Justification
{
    public const MAX_LENGTH = 1000;

    private function __construct(public readonly string $text)
    {
    }

    public static function of(?string $text): self
    {
        $trimmed = trim((string) $text);

        if ($trimmed === '' || mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidJustification();
        }

        return new self($trimmed);
    }
}
