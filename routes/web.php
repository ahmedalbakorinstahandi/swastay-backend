<?php

use Illuminate\Support\Facades\Route;


// invoice route
Route::get('/invoice', function () {
    return view('invoice');
});

Route::get('/', function () {
    return response()->json(
        [
            'success' => false,
            'message' => 'API only'
        ]
    );
});
