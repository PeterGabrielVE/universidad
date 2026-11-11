<?php

namespace App\Modules\IdentityAccess\Application\UseCase;

use App\Modules\IdentityAccess\Application\Dto\UserData;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\RoleName;
use App\Modules\IdentityAccess\Domain\UserAccount;

/**
 * Registro público (FUNCTIONAL_SPECIFICATIONS.md §5): siempre como Aspirante y
 * activo, sin programa. Lo que envíe el formulario no puede elegir otro rol.
 */
final class RegisterApplicant
{
    public function __construct(private readonly CreateUser $createUser)
    {
    }

    public function execute(
        string $name,
        string $lastName,
        int $identificationCard,
        string $email,
        string $password,
    ): UserAccount {
        return $this->createUser->execute(new UserData(
            name: $name,
            lastName: $lastName,
            identificationCard: $identificationCard,
            email: $email,
            phone: null,
            role: RoleName::Aspirante->value,
            status: AccountStatus::Activo,
            programIds: [],
            password: $password,
        ));
    }
}
