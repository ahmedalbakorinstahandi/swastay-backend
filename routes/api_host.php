<?php

//  group prefixe host and middleware sanctum

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingRuleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'host', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'create']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'delete']);

    Route::put('/listings/{id}/available-dates', [ListingController::class, 'updateAvailableDate']);
    Route::put('/listings/{id}/rules', [ListingController::class, 'updateRule']);

    Route::group(['prefix' => 'host', 'middleware' => ['auth:sanctum']], function () {
        Route::get('/listing-rules/{id}', [ListingRuleController::class, 'show']);
        Route::post('/listing-rules', [ListingRuleController::class, 'create']);
        Route::put('/listing-rules/{id}', [ListingRuleController::class, 'update']);
        Route::delete('/listing-rules/{id}', [ListingRuleController::class, 'destroy']);


        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
    });
});
