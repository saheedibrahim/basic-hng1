<?php

use App\Http\Controllers\BasicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/greet', [BasicController::class, 'greet']);
Route::get('getip', [BasicController::class, 'getLocationByIp']);