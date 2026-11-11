<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Programas que un usuario del personal puede ver (FUNCTIONAL_SPECIFICATIONS.md §7, §15).
 *
 * El Coordinador queda limitado a los programas que tiene asignados (uno o varios);
 * el resto del personal no tiene límite.
 */
final class ProgramScope
{
    /** @param list<int>|null $programIds null = sin límite */
    private function __construct(private readonly ?array $programIds)
    {
    }

    public static function unrestricted(): self
    {
        return new self(null);
    }

    /** @param list<int> $programIds programas asignados al usuario */
    public static function forStaff(RoleName $role, array $programIds): self
    {
        if ($role !== RoleName::Coordinador) {
            return self::unrestricted();
        }

        return new self(array_values(array_unique(array_map('intval', $programIds))));
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

    /**
     * ¿Comparte algún programa? Un estudiante con doble titulación es visible para
     * el coordinador de cualquiera de sus programas.
     *
     * @param list<int> $programIds
     */
    public function coversAny(array $programIds): bool
    {
        if ($this->programIds === null) {
            return true;
        }

        return array_intersect($this->programIds, array_map('intval', $programIds)) !== [];
    }
}
