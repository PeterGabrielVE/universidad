<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\InvalidGrade;

/**
 * Nota en escala 0–100 con hasta dos decimales (FUNCTIONAL_SPECIFICATIONS.md §8).
 */
final class Grade
{
    private function __construct(public readonly float $value)
    {
    }

    public static function of(float|int|string $value): self
    {
        if (! is_numeric($value)) {
            throw new InvalidGrade((string) $value);
        }

        $number = round((float) $value, 2);

        if ($number < 0 || $number > 100) {
            throw new InvalidGrade((string) $value);
        }

        return new self($number);
    }

    public function isAtLeast(self $other): bool
    {
        return $this->value >= $other->value;
    }
}
