<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

/**
 * La cédula es el ancla de la identidad (FUNCTIONAL_SPECIFICATIONS.md §13): una
 * persona tiene una sola cuenta. Para cambiar el correo de una cuenta existente,
 * Control de Estudios la edita; no se crea otra.
 */
final class IdentificationCardAlreadyInUse extends DomainException
{
    public function __construct(int $identificationCard)
    {
        parent::__construct(
            "Ya existe una cuenta con la cédula {$identificationCard}. "
            . 'Si perdiste el acceso a tu correo, solicita a Control de Estudios que lo actualice.'
        );
    }

    public function field(): ?string
    {
        return 'identification_card';
    }
}
