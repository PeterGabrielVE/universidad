<?php

namespace App\Modules\IdentityAccess\Application\Port;

interface TransactionManager
{
    /**
     * @template T
     * @param callable(): T $work
     * @return T
     */
    public function run(callable $work): mixed;
}
