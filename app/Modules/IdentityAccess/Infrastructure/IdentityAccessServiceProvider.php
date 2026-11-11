<?php

namespace App\Modules\IdentityAccess\Infrastructure;

use App\Modules\IdentityAccess\Application\Port\PasswordHasher;
use App\Modules\IdentityAccess\Application\Port\RoleRepository;
use App\Modules\IdentityAccess\Application\Port\StudentRecords;
use App\Modules\IdentityAccess\Application\Port\TransactionManager;
use App\Modules\IdentityAccess\Application\Port\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Conecta los puertos del módulo con sus adaptadores.
 */
final class IdentityAccessServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepository::class => EloquentUserRepository::class,
        RoleRepository::class => SpatieRoleRepository::class,
        PasswordHasher::class => LaravelPasswordHasher::class,
        StudentRecords::class => EloquentStudentRecords::class,
        TransactionManager::class => DatabaseTransactionManager::class,
    ];
}
