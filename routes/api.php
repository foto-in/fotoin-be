<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['cors', 'json.response']], function(){

    // Authentication
//    Route:pos('/login', [AuthController::class, 'login']);
//    Route:pos('/register', [AuthController::class, 'register']);

    // Only User can access

});
