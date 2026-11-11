<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\InvalidLapsePeriod;
use DateTimeImmutable;

/**
 * Fechas de inicio y fin de un lapso. El nombre visible se deriva de ellas
 * con el formato histórico: "Lapso Julio 2026-Diciembre 2026".
 */
final class LapsePeriod
{
    private const MONTHS = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    private function __construct(
        public readonly DateTimeImmutable $start,
        public readonly DateTimeImmutable $end,
    ) {
    }

    public static function between(string $start, string $end): self
    {
        $startDate = DateTimeImmutable::createFromFormat('!Y-m-d', $start);
        $endDate = DateTimeImmutable::createFromFormat('!Y-m-d', $end);

        if ($startDate === false || $endDate === false
            || $startDate->format('Y-m-d') !== $start || $endDate->format('Y-m-d') !== $end) {
            throw InvalidLapsePeriod::badDate($start, $end);
        }

        if ($endDate <= $startDate) {
            throw InvalidLapsePeriod::endNotAfterStart();
        }

        return new self($startDate, $endDate);
    }

    public function name(): string
    {
        return sprintf(
            'Lapso %s %s-%s %s',
            self::MONTHS[(int) $this->start->format('n')],
            $this->start->format('Y'),
            self::MONTHS[(int) $this->end->format('n')],
            $this->end->format('Y'),
        );
    }
}
