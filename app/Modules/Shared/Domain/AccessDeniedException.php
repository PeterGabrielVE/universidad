<?php

namespace App\Modules\Shared\Domain;

/**
 * El usuario tiene el permiso general pero el recurso queda fuera de su alcance
 * (p. ej. un lapso de otro programa). La capa de presentación la traduce a 403.
 */
abstract class AccessDeniedException extends DomainException
{
}
