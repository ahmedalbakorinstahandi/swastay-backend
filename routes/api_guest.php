<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'guest'], function () {


    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);


    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);

        Route::put('/listings/{id}/favorites', [ListingController::class, 'listingFavoritesUpdate']);
    });
});
