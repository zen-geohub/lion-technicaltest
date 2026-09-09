<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('departments', DepartmentController::class)
        ->except(['store', 'update', 'destroy'])
        ->names('departments.read');

    Route::middleware('admin')->group(function () {
        Route::apiResource('departments', DepartmentController::class)
            ->only(['store', 'update', 'destroy']);
    });
});
