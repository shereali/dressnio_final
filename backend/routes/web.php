<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'Dressnio Ecommerce API Engine Live & Operational',
        'version' => '1.0.0',
    ]);
});
