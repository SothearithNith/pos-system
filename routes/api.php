
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {

    // Authentication
    Route::post('/login', [AuthController::class, 'login'])
        ->name('api.v1.login');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.v1.logout');

        Route::get('/user', [AuthController::class, 'user'])
            ->name('api.v1.user');
    });
});
