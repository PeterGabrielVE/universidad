<?php

namespace App\Modules\IdentityAccess\Application\Port;

use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\UserAccount;

interface UserRepository
{
    public function find(int $id): ?UserAccount;

    public function emailInUse(Email $email, ?int $exceptUserId = null): bool;

    public function identificationCardInUse(int $identificationCard, ?int $exceptUserId = null): bool;

    /**
     * Inserta o actualiza la cuenta y su rol. $passwordHash null conserva la
     * contraseña actual. Devuelve la cuenta con su id.
     */
    public function save(UserAccount $account, ?string $passwordHash = null): UserAccount;

    public function delete(int $id): void;
}
