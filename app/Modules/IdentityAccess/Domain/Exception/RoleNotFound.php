<?php

namespace App\Modules\IdentityAccess\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class RoleNotFound extends NotFoundException
{
    public static function withId(int $id): self
    {
        return new self("No existe el rol {$id}.");
    }

    public static function withName(string $name): self
    {
        return new self("No existe el rol «{$name}».");
    }

    public function field(): ?string
    {
        return 'role';
    }
}
