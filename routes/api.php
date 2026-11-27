<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ColorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Sign in (max 5 attempts per minute per IP)
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('api.v1.login');

    // Get a new access token. Only a REFRESH token is accepted here.
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware(['auth:sanctum', 'abilities:refresh', 'throttle:10,1'])
        ->name('api.v1.refresh');

    // Everything else needs an ACCESS token (a refresh token is rejected).
    Route::middleware(['auth:sanctum', 'abilities:access'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.logout');

        Route::get('/me', [AuthController::class, 'me'])->name('api.v1.me');
        Route::get('/user', [AuthController::class, 'me'])->name('api.v1.user');   // old URL

        Route::apiResource('colors', ColorController::class);

        // ---- protect each route with its permission ----
        // Route::get('/products', [ProductController::class, 'index'])->middleware('permission:products.view');
        // Route::post('/sales', [SaleController::class, 'store'])->middleware('permission:sales.create');
    });
});
