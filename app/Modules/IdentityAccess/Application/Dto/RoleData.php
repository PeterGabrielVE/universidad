<?php

namespace App\Modules\IdentityAccess\Application\Dto;

use App\Modules\IdentityAccess\Domain\RoleName;

final class RoleData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }

    public function isSystemRole(): bool
    {
        return RoleName::isSystemRole($this->name);
    }

    /** @return array{id: int, name: string, description: ?string, system: bool} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'system' => $this->isSystemRole(),
        ];
    }
}
