<?php

namespace App\Modules\IdentityAccess\Application\UseCase;

use App\Modules\IdentityAccess\Application\Dto\UserData;
use App\Modules\IdentityAccess\Application\Port\PasswordHasher;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use App\Modules\IdentityAccess\Application\Port\StudentRecords;
use App\Modules\Shared\Application\Port\TransactionManager;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\Exception\EmailAlreadyInUse;
use App\Modules\IdentityAccess\Domain\Exception\IdentificationCardAlreadyInUse;
use App\Modules\IdentityAccess\Domain\Exception\PasswordRequired;
use App\Modules\IdentityAccess\Domain\Exception\RoleNotFound;
use App\Modules\IdentityAccess\Domain\UserAccount;

/**
 * Alta de una cuenta por un administrador. Si el rol es Estudiante se crea
 * también su expediente, en la misma transacción.
 */
final class CreateUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RoleRepository $roles,
        private readonly PasswordHasher $hasher,
        private readonly StudentRecords $studentRecords,
        private readonly TransactionManager $transaction,
    ) {
    }

    public function execute(UserData $data): UserAccount
    {
        if ($data->password === null || $data->password === '') {
            throw new PasswordRequired();
        }

        $email = Email::from($data->email);

        if ($this->users->emailInUse($email)) {
            throw new EmailAlreadyInUse($email);
        }

        if ($data->identificationCard !== null && $this->users->identificationCardInUse($data->identificationCard)) {
            throw new IdentificationCardAlreadyInUse($data->identificationCard);
        }

        if ($this->roles->findByName($data->role) === null) {
            throw RoleNotFound::withName($data->role);
        }

        $account = UserAccount::create(
            $data->name,
            $data->lastName,
            $data->identificationCard,
            $email,
            $data->phone,
            $data->role,
            $data->status,
            $data->programIds,
        );
        $hash = $this->hasher->hash($data->password);

        return $this->transaction->run(function () use ($account, $hash) {
            $saved = $this->users->save($account, $hash);

            if ($saved->isStudent()) {
                $this->studentRecords->ensureFor($saved->id());
            }

            return $saved;
        });
    }
}
