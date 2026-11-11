<?php

namespace App\Modules\Shared\Domain;

use RuntimeException;

/**
 * Violación de una regla de negocio. La capa de presentación la traduce a una
 * respuesta 422 con el mensaje, que está pensado para el usuario final.
 */
abstract class DomainException extends RuntimeException
{
    /** Campo del formulario al que se refiere el error, si hay uno. */
    public function field(): ?string
    {
        return null;
    }
}
