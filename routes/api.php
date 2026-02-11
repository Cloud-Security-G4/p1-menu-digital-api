<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
    Route::prefix('admin')->group(function () {
        Route::get('/restaurant', [RestaurantController::class, 'index']);
        Route::post('/restaurant', [RestaurantController::class, 'store']);
        Route::put('/restaurant', [RestaurantController::class, 'update']);
        Route::delete('/restaurant', [RestaurantController::class, 'destroy']);
    });
});



Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:api')->prefix('v1/admin')->group(function () {
    Route::get('/restaurante', [RestaurantController::class, 'index']);
    Route::post('/restaurante', [RestaurantController::class, 'store']);
    Route::put('/restaurante', [RestaurantController::class, 'update']);
    Route::delete('/restaurante', [RestaurantController::class, 'destroy']);
});
