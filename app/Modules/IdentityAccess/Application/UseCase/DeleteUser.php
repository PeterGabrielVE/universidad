<?php

namespace App\Modules\IdentityAccess\Application\UseCase;

use App\Modules\IdentityAccess\Application\Port\UserRepository;
use App\Modules\IdentityAccess\Domain\Exception\UserNotFound;

final class DeleteUser
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function execute(int $id): void
    {
        if ($this->users->find($id) === null) {
            throw new UserNotFound($id);
        }

        $this->users->delete($id);
    }
}
