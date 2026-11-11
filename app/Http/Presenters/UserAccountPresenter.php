<?php

namespace App\Http\Presenters;

use App\Modules\IdentityAccess\Domain\UserAccount;

/**
 * Forma pública de una cuenta en las respuestas (la misma que UserDirectory::summary).
 */
final class UserAccountPresenter
{
    /** @return array{id: int, name: string, last_name: string, email: string, role: string, status: string, program_id: ?int} */
    public static function toArray(UserAccount $account): array
    {
        return [
            'id' => $account->id(),
            'name' => $account->name(),
            'last_name' => $account->lastName(),
            'email' => $account->email()->value,
            'role' => $account->role(),
            'status' => $account->status()->value,
            'program_id' => $account->programId(),
        ];
    }
}
