<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Controllers\PhotographerController;
use App\Http\Middleware\FotographerAuth;
use App\Http\Middleware\UserAuth;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PortofolioController;



Route::middleware(['every-request'])->group(function (){

    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Only Authenticated User can access
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::get('/booking', [BookingController::class, 'getAllBooking']);

    });


    // Only Photographer can access
    Route::middleware(['auth:sanctum', FotographerAuth::class])->group(function () {
        Route::post('/portofolio', [PhotographerController::class, 'uploadPortofolio']);
        Route::get('/booking/{id}', [BookingController::class, 'getDetailBookingPhotographer']);
        Route::post('/booking/{booking_id}', [BookingController::class, 'acceptOrder']);
        Route::post('/gallery/{booking_id}', [GalleryController::class, 'uploadOrder']);
        Route::post('/preview/{booking_id}', [GalleryController::class, 'uploadPreview']);
    });

    // Only User can access
    Route::middleware(['auth:sanctum', UserAuth::class])->group(function () {
        Route::post('/photographer', [PhotographerController::class, 'register']);
        Route::post('/booking', [BookingController::class, 'createBooking']);
        Route::post('/payment/{booking_id}', [BookingController::class, 'payOrder']);
        Route::get('/preview/{booking_id}', [GalleryController::class, 'getPreviewGallery']);
        Route::get('/gallery/{user_id}/{booking_id}', [GalleryController::class, 'getDetailGallery']);
        Route::get('/gallery/{user_id}', [GalleryController::class, 'getAllGallery']);
        Route::delete('/gallery/{user_id}/{booking_id}', [GalleryController::class, 'deleteGallery']);
    });


    Route::get('/portofolio/{id}', [PortofolioController::class, 'getAllPortofolio']);
    Route::get('/portofolio/{photographer_id}/{id}', [PortofolioController::class, 'getDetailPortofolio']);
    Route::get('/search', [PhotographerController::class, 'searchPhotographer']);
});
