<?php

namespace Tests\Support\IdentityAccess;

use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\UserAccount;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<int, UserAccount> */
    public array $accounts = [];

    /** @var array<int, string> */
    public array $passwordHashes = [];

    private int $nextId = 1;

    public function find(int $id): ?UserAccount
    {
        return isset($this->accounts[$id]) ? clone $this->accounts[$id] : null;
    }

    public function emailInUse(Email $email, ?int $exceptUserId = null): bool
    {
        foreach ($this->accounts as $id => $account) {
            if ($id !== $exceptUserId && $account->email()->equals($email)) {
                return true;
            }
        }

        return false;
    }

    public function identificationCardInUse(int $identificationCard, ?int $exceptUserId = null): bool
    {
        foreach ($this->accounts as $id => $account) {
            if ($id !== $exceptUserId && $account->identificationCard() === $identificationCard) {
                return true;
            }
        }

        return false;
    }

    public function save(UserAccount $account, ?string $passwordHash = null): UserAccount
    {
        $id = $account->id() ?? $this->nextId++;
        $stored = UserAccount::restore(
            $id,
            $account->name(),
            $account->lastName(),
            $account->identificationCard(),
            $account->email(),
            $account->phone(),
            $account->role(),
            $account->status(),
            $account->programIds(),
        );
        $this->accounts[$id] = $stored;

        if ($passwordHash !== null) {
            $this->passwordHashes[$id] = $passwordHash;
        }

        return clone $stored;
    }

    public function delete(int $id): void
    {
        unset($this->accounts[$id], $this->passwordHashes[$id]);
    }
}
