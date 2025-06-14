<?php

use App\Http\Controllers\UserVerificationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/verifications', [UserVerificationController::class, 'index']);
    Route::get('/verifications/{id}', [UserVerificationController::class, 'show']);
    Route::post('/verifications', [UserVerificationController::class, 'create']);
    Route::delete('/verifications/{id}', [UserVerificationController::class, 'destroy']);
});
