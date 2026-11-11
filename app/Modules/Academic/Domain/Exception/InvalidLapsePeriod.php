<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class InvalidLapsePeriod extends DomainException
{
    public static function badDate(string $start, string $end): self
    {
        return new self("Las fechas «{$start}» y «{$end}» no son válidas (formato AAAA-MM-DD).");
    }

    public static function endNotAfterStart(): self
    {
        return new self('La fecha final debe ser posterior a la fecha de inicio.');
    }

    public function field(): ?string
    {
        return 'end_lapse';
    }
}
