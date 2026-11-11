<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\Shared\Domain\DomainException;

final class InvalidLapseTransition extends DomainException
{
    public static function between(LapseStatus $from, LapseStatus $to): self
    {
        return new self("Un lapso «{$from->label()}» no puede pasar a «{$to->label()}».");
    }

    public static function cannotCreateClosed(): self
    {
        return new self('Un lapso nuevo no puede crearse cerrado.');
    }

    public function field(): ?string
    {
        return 'status';
    }
}
