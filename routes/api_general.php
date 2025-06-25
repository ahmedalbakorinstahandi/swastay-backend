<?php


use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HouseTypeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'general'], function () {
    Route::post('images/upload', [ImageController::class, 'uploadImage']);
    Route::post('files/upload', [ImageController::class, 'uploadFile']);



    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);


    Route::get('/house-types', [HouseTypeController::class, 'index']);
    Route::get('/house-types/{id}', [HouseTypeController::class, 'show']);


    Route::get('/features', [FeatureController::class, 'index']);
    Route::get('/features/{id}', [FeatureController::class, 'show']);

    Route::get('/cities', [CityController::class, 'index']);

    Route::get('/settings', [SettingController::class, 'index']);
    Route::get('/settings/{id}', [SettingController::class, 'show']);


    Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/unread-count', 'unreadCount');
        Route::get('{id}', 'show');
    });
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::post('/{id}/read',  'readNotification');
        });
    });
});
