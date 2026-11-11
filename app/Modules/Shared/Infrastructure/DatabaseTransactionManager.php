<?php

namespace App\Modules\Shared\Infrastructure;

use App\Modules\Shared\Application\Port\TransactionManager;
use Illuminate\Database\ConnectionInterface;

final class DatabaseTransactionManager implements TransactionManager
{
    public function __construct(private readonly ConnectionInterface $connection)
    {
    }

    public function run(callable $work): mixed
    {
        return $this->connection->transaction($work);
    }
}
