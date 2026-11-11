<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Models\User;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\Email;
use App\Modules\IdentityAccess\Domain\UserAccount;

/**
 * Persiste UserAccount en la tabla users. El rol se guarda solo en Spatie.
 */
final class EloquentUserRepository implements UserRepository
{
    public function find(int $id): ?UserAccount
    {
        $user = User::with('roles')->find($id);

        return $user === null ? null : $this->toDomain($user);
    }

    public function emailInUse(Email $email, ?int $exceptUserId = null): bool
    {
        return User::query()
            ->whereRaw('LOWER(email) = ?', [$email->value])
            ->when($exceptUserId !== null, fn ($q) => $q->whereKeyNot($exceptUserId))
            ->exists();
    }

    public function save(UserAccount $account, ?string $passwordHash = null): UserAccount
    {
        $user = $account->id() === null ? new User() : User::findOrFail($account->id());

        $user->forceFill([
            'name' => $account->name(),
            'last_name' => $account->lastName(),
            'identification_card' => $account->identificationCard(),
            'email' => $account->email()->value,
            'phone' => $account->phone(),
            'status' => $account->status(),
            'doctorado_id' => $account->programId(),
        ]);

        if ($passwordHash !== null) {
            $user->password = $passwordHash;
        }

        $user->save();
        $user->syncRoles([$account->role()]);

        return $this->toDomain($user->load('roles'));
    }

    public function delete(int $id): void
    {
        User::whereKey($id)->delete();
    }

    private function toDomain(User $user): UserAccount
    {
        return UserAccount::restore(
            $user->id,
            $user->name,
            (string) $user->last_name,
            $user->identification_card !== null ? (int) $user->identification_card : null,
            Email::from($user->email),
            $user->phone,
            (string) $user->roles->first()?->name,
            $user->status,
            $user->doctorado_id !== null ? (int) $user->doctorado_id : null,
        );
    }
}
