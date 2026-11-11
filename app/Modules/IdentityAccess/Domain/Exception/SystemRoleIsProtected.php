<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

/**
 * Los roles del sistema sostienen la matriz de permisos: renombrarlos o
 * eliminarlos dejaría usuarios sin acceso o con acceso incorrecto.
 */
final class SystemRoleIsProtected extends DomainException
{
    public function __construct(string $name)
    {
        parent::__construct("«{$name}» es un rol del sistema: no se puede renombrar ni eliminar.");
    }

    public function field(): ?string
    {
        return 'name';
    }
}
