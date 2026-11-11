<?php

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Modules\IdentityAccess\Domain\Permission as P;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Las pantallas Vue se autentican con la cookie de sesión (Sanctum en modo
| SPA, ver config/sanctum.php "stateful"). Se autoriza por permiso.
| Prefijo "api." en los nombres para no chocar con las rutas web.
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'active'])->name('api.')->group(function () {
    Route::apiResource('roles', RoleController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware(P::middleware(P::RolesManage));

    Route::prefix('v1')->group(function () {
        Route::apiResource('users', UserController::class)->only(['index', 'show'])
            ->middleware(P::middleware(P::UsersView));
        Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy'])
            ->middleware(P::middleware(P::UsersManage));
    });
});
