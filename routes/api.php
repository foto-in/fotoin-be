<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Controllers\PhotographerController;
use App\Http\Middleware\FotographerAuth;
use App\Http\Middleware\UserAuth;


Route::middleware(['every-request'])->group(function (){

    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Only Authenticated User can access
    Route::middleware(['auth:sanctum', UserAuth::class])->group(function () {
        Route::post('/photographer', [PhotographerController::class, 'register']);
    });


    // Only Photographer can access
    Route::middleware(['auth:sanctum', FotographerAuth::class])->group(function () {
        Route::post('/portofolio', [PhotographerController::class, 'uploadPortofolio']);
    });


    Route::get('/portofolio/{username}', [PortofolioController::class, 'getAllPortofolio']);
    Route::get('/portofolio/{username}/{id}', [PortofolioController::class, 'getDetailPortofolio']);

});
