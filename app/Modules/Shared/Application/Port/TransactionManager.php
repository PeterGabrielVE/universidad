<?php

namespace App\Modules\Shared\Application\Port;

interface TransactionManager
{
    /**
     * @template T
     * @param callable(): T $work
     * @return T
     */
    public function run(callable $work): mixed;
}
