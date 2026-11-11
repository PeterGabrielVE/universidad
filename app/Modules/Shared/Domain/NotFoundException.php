<?php

namespace App\Modules\Shared\Domain;

/**
 * El recurso solicitado no existe. La capa de presentación la traduce a 404.
 */
abstract class NotFoundException extends DomainException
{
}
