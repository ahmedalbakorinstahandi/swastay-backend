<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// auth group
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', function () {
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'status' => 200,
        ]);
    });
});
