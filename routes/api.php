<?php

use App\Authorization\Roles;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Las pantallas Vue se autentican con la cookie de sesión (Sanctum en modo
| SPA, ver config/sanctum.php "stateful"). No se emiten tokens desde el front.
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Prefijo "api." en los nombres: sin él chocaban con las rutas web "roles.*" e impedían route:cache.
Route::middleware(['auth:sanctum', Roles::middleware(Roles::ADMIN)])->name('api.')->group(function () {
    Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
