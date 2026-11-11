<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Modules\IdentityAccess\Application\Port\TransactionManager;
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
