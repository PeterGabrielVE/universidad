<?php

namespace App\Modules\Academic\Domain;

/**
 * Estado de un lapso o cohorte (FUNCTIONAL_SPECIFICATIONS.md §10).
 * Ciclo: planificacion → abierto → cerrado. Cerrado es definitivo.
 */
enum LapseStatus: string
{
    case Planificacion = 'planificacion';
    case Abierto = 'abierto';
    case Cerrado = 'cerrado';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Planificacion => $target === self::Abierto,
            self::Abierto => $target === self::Cerrado,
            self::Cerrado => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Planificacion => 'En planificación',
            self::Abierto => 'Abierto',
            self::Cerrado => 'Cerrado',
        };
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
