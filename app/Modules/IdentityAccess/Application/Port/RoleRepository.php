<?php

namespace App\Modules\IdentityAccess\Application\Port;

use App\Modules\IdentityAccess\Application\Dto\RoleData;

interface RoleRepository
{
    /** @return list<RoleData> */
    public function all(): array;

    public function findById(int $id): ?RoleData;

    public function findByName(string $name): ?RoleData;

    public function create(string $name, ?string $description): RoleData;

    public function update(int $id, string $name, ?string $description): RoleData;

    public function delete(int $id): void;
}
