<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\FolderController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/folders', [FolderController::class, 'index']);
    Route::get('/folders/{folder}', [FolderController::class, 'show']);
    Route::get('/folders/{folder}/breadcrumb', [FolderController::class, 'breadcrumb']);

    Route::middleware('admin')->group(function () {
        Route::post('/folders', [FolderController::class, 'store']);
        Route::put('/folders/{folder}', [FolderController::class, 'update']);
        Route::delete('/folders/{folder}', [FolderController::class, 'destroy']);
    });
});
