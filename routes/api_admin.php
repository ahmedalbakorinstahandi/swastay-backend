<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HouseTypeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingReviewController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserVerificationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'create']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);

    Route::put('/listings/{id}/available-dates', [ListingController::class, 'updateAvailableDate']);
    Route::put('/listings/{id}/rules', [ListingController::class, 'updateRule']);

    Route::put('/listings/{id}/images/{image_id}/reorder', [ListingController::class, 'reorderImage']);


    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


    Route::get('/house-types', [HouseTypeController::class, 'index']);
    Route::get('/house-types/{id}', [HouseTypeController::class, 'show']);
    Route::post('/house-types', [HouseTypeController::class, 'create']);
    Route::put('/house-types/{id}', [HouseTypeController::class, 'update']);
    Route::delete('/house-types/{id}', [HouseTypeController::class, 'destroy']);

    Route::get('/features', [FeatureController::class, 'index']);
    Route::get('/features/{id}', [FeatureController::class, 'show']);
    Route::post('/features', [FeatureController::class, 'create']);
    Route::put('/features/{id}', [FeatureController::class, 'update']);
    Route::delete('/features/{id}', [FeatureController::class, 'destroy']);


    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'create']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);


    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);


    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'create']);
    // Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    Route::post('/bookings/{id}/transactions', [BookingController::class, 'addTransaction']);



    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    // Route::post('/transactions', [TransactionController::class, 'create']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);



    Route::group(['prefix' => 'reviews'], function () {
        Route::get('/', [ListingReviewController::class, 'index']);
        Route::get('/{id}', [ListingReviewController::class, 'show']);
        Route::put('/{id}', [ListingReviewController::class, 'update']);
        Route::delete('/{id}', [ListingReviewController::class, 'destroy']);
    });

    Route::group(['prefix' => 'verifications'], function () {
        Route::get('/', [UserVerificationController::class, 'index']);
        Route::get('/{id}', [UserVerificationController::class, 'show']);
        Route::put('/{id}', [UserVerificationController::class, 'update']);
        Route::delete('/{id}', [UserVerificationController::class, 'destroy']);
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::get('/{id}', [SettingController::class, 'show']);
        Route::post('/', [SettingController::class, 'create']);
        Route::put('/', [SettingController::class, 'update']);
        Route::delete('/{id}', [SettingController::class, 'delete']);
    });
});
