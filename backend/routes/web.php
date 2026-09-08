<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Data retrieved successfully',
        'data' => [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]
    ]);
});
