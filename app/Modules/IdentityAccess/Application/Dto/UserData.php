<?php

namespace App\Modules\IdentityAccess\Application\Dto;

use App\Modules\IdentityAccess\Domain\AccountStatus;

/**
 * Datos de alta o edición de una cuenta, ya validados en su forma por la capa
 * de presentación. $password null en una edición conserva la contraseña.
 */
final class UserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $lastName,
        public readonly ?int $identificationCard,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly string $role,
        public readonly AccountStatus $status,
        public readonly ?int $programId,
        public readonly ?string $password,
    ) {
    }
}
