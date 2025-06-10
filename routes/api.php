<?php

use App\Http\Middleware\SetLocaleMiddleware;
use Illuminate\Routing\Route;

Route::middleware(SetLocaleMiddleware::class)->group(function () {
    require_once __DIR__ . '/api_auth.php';
    require_once __DIR__ . '/api_guest.php';
    require_once __DIR__ . '/api_admin.php';
    require_once __DIR__ . '/api_host.php';
    require_once __DIR__ . '/api_general.php';
});
