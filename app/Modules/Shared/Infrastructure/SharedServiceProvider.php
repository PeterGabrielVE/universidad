<?php

namespace App\Modules\Shared\Infrastructure;

use App\Modules\Shared\Application\Port\TransactionManager;
use Illuminate\Support\ServiceProvider;

/**
 * Puertos comunes a todos los módulos.
 */
final class SharedServiceProvider extends ServiceProvider
{
    public array $bindings = [
        TransactionManager::class => DatabaseTransactionManager::class,
    ];
}
