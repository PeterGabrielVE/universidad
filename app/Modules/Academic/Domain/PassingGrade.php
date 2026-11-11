<?php

namespace App\Modules\Academic\Domain;

/**
 * Nota mínima aprobatoria de un programa (FUNCTIONAL_SPECIFICATIONS.md §3).
 * Una nota igual a la mínima aprueba.
 */
final class PassingGrade
{
    /** Base confirmada; cada programa puede definir la suya (p. ej. 75 en un doctorado). */
    public const DEFAULT = 48;

    private function __construct(private readonly Grade $minimum)
    {
    }

    public static function of(float|int|string $minimum): self
    {
        return new self(Grade::of($minimum));
    }

    public static function default(): self
    {
        return self::of(self::DEFAULT);
    }

    public function evaluate(Grade $grade): EnrollmentResult
    {
        return $grade->isAtLeast($this->minimum) ? EnrollmentResult::Aprobado : EnrollmentResult::Reprobado;
    }

    public function value(): float
    {
        return $this->minimum->value;
    }
}
