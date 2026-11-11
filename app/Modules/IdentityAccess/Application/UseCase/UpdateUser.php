<?php

namespace App\Modules\IdentityAccess\Application\UseCase;

use App\Modules\IdentityAccess\Application\Dto\UserData;
use App\Modules\IdentityAccess\Application\Port\PasswordHasher;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use App\Modules\IdentityAccess\Application\Port\StudentRecords;
use App\Modules\IdentityAccess\Application\Port\TransactionManager;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\Exception\EmailAlreadyInUse;
use App\Modules\IdentityAccess\Domain\Exception\RoleNotFound;
use App\Modules\IdentityAccess\Domain\Exception\UserNotFound;
use App\Modules\IdentityAccess\Domain\UserAccount;

/**
 * Edición de una cuenta: datos, rol y estado. Sin contraseña, se conserva la actual.
 */
final class UpdateUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly PasswordHasher $hasher,
        private readonly StudentRecords $studentRecords,
        private readonly TransactionManager $transaction,
    ) {
    }

    public function execute(int $id, UserData $data): UserAccount
    {
        $account = $this->users->find($id) ?? throw new UserNotFound($id);
        $email = Email::from($data->email);

        if ($this->users->emailInUse($email, $id)) {
            throw new EmailAlreadyInUse($email);
        }

        if ($this->roles->findByName($data->role) === null) {
            throw RoleNotFound::withName($data->role);
        }

        $account->updateProfile(
            $data->name,
            $data->lastName,
            $data->identificationCard,
            $email,
            $data->phone,
            $data->programId,
        );
        $account->changeRole($data->role);
        $account->changeStatus($data->status);

        $hash = ($data->password === null || $data->password === '') ? null : $this->hasher->hash($data->password);

        return $this->transaction->run(function () use ($account, $hash) {
            $saved = $this->users->save($account, $hash);

            if ($saved->isStudent()) {
                $this->studentRecords->ensureFor($saved->id());
            }

            return $saved;
        });
    }
}
