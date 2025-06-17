<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/roles', [RoleController::class, 'index']);
Route::resource('roles', RoleController::class);


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
});

