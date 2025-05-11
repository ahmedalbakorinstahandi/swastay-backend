<?php

use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'guest'], function () {


    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);



    Route::group(['middleware' => ['auth:sanctum']], function () {});
});
