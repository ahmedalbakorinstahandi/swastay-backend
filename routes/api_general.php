<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HouseTypeController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'general'], function () {
    Route::post('images/upload', [ImageController::class, 'uploadImage']);


    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);


    Route::get('/house-types', [HouseTypeController::class, 'index']);
    Route::get('/house-types/{id}', [HouseTypeController::class, 'show']);
});
