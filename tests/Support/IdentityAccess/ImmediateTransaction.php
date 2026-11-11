<?php

namespace Tests\Support\IdentityAccess;

use App\Modules\IdentityAccess\Application\Port\TransactionManager;

final class ImmediateTransaction implements TransactionManager
{
    public int $runs = 0;

    public function run(callable $work): mixed
    {
        $this->runs++;

        return $work();
    }
}
