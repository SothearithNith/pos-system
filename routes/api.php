<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Route::apiResource('/products', ProductController::class);
        // Route::apiResource('/categories', CategoryController::class);
        // Route::apiResource('/customers', CustomerController::class);
        // Route::apiResource('/orders', OrderController::class);
    });
});
