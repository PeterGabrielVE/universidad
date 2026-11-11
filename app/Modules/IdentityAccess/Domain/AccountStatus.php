<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Estado de una cuenta (FUNCTIONAL_SPECIFICATIONS.md §10).
 */
enum AccountStatus: string
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case Suspendido = 'suspendido';

    public function canLogIn(): bool
    {
        return $this === self::Activo;
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
