<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Programas de doctorado que un usuario puede ver (FUNCTIONAL_SPECIFICATIONS.md §2, §7).
 *
 * El Coordinador queda limitado a su programa; el resto del personal no tiene
 * límite. El programa 3 significa "ambos": un coordinador con 3 ve los dos, y un
 * estudiante con 3 pertenece a los dos.
 */
final class ProgramScope
{
    public const GERENCIA = 1;
    public const CIENCIAS = 2;
    public const AMBOS = 3;

    /** @param list<int>|null $programIds null = sin límite */
    private function __construct(private readonly ?array $programIds)
    {
    }

    public static function unrestricted(): self
    {
        return new self(null);
    }

    public static function forStaff(RoleName $role, ?int $programId): self
    {
        if ($role !== RoleName::Coordinador) {
            return self::unrestricted();
        }

        return match ($programId) {
            self::AMBOS => new self([self::GERENCIA, self::CIENCIAS, self::AMBOS]),
            null => new self([]),
            default => new self([$programId, self::AMBOS]),
        };
    }

    public function isUnrestricted(): bool
    {
        return $this->programIds === null;
    }

    /** @return list<int>|null null = sin límite */
    public function programIds(): ?array
    {
        return $this->programIds;
    }

    public function covers(?int $programId): bool
    {
        if ($this->programIds === null) {
            return true;
        }

        return $programId !== null && in_array($programId, $this->programIds, true);
    }
}
