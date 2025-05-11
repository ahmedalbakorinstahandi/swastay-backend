<?php

//  group prefixe host and middleware sanctum

use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'host', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'create']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'delete']);
});
