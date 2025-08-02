<?php

use App\Http\Middleware\SetLocaleMiddleware;
use App\Models\DigiBankarWebhookController;
use Illuminate\Support\Facades\Route;


Route::post('/webhook/digibankar', [DigiBankarWebhookController::class, 'handle']);


Route::middleware(SetLocaleMiddleware::class)->group(function () {
    require_once __DIR__ . '/api_auth.php';
    require_once __DIR__ . '/api_guest.php';
    require_once __DIR__ . '/api_admin.php';
    require_once __DIR__ . '/api_host.php';
    require_once __DIR__ . '/api_user.php';
    require_once __DIR__ . '/api_general.php';
});
