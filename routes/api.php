<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ForceJsonResponse;


Route::middleware([Cors::class, ForceJsonResponse::class])->group(function (){

    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Only User can access

});
