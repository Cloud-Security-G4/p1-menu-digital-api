<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DishController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:api')->prefix('/admin')->group(function () {
        Route::get('/restaurant', [RestaurantController::class, 'index']);
        Route::post('/restaurant', [RestaurantController::class, 'store']);
        Route::put('/restaurant', [RestaurantController::class, 'update']);
        Route::delete('/restaurant/{id}', [RestaurantController::class, 'destroy']);

        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        Route::patch('/categories/reorder', [CategoryController::class, 'reorder']);

        Route::get('/dishes', [DishController::class, 'index']);
        Route::get('/dishes/{id}', [DishController::class, 'show']);
        Route::post('/dishes', [DishController::class, 'store']);
        Route::put('/dishes/{id}', [DishController::class, 'update']);
        Route::delete('/dishes/{id}', [DishController::class, 'destroy']);
        Route::patch('/dishes/{id}/availability', [DishController::class, 'toggleAvailability']);
        Route::get('/qr', [MenuController::class, 'getRestaurantMenuQr']);

        Route::post('/upload', [ImageController::class, 'upload']);
        Route::delete('/upload/{filename}', [ImageController::class, 'delete']);
    });

    Route::get('/menu/{slug}', [MenuController::class, 'getPublicMenu']);
});

Route::get('/test', [ImageController::class, 'test']);
