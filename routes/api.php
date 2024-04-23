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

        // Request to booking
        Route::post('/booking', [BookingController::class, 'createBooking']);
    });


    // Only Photographer can access
    Route::middleware(['auth:sanctum', FotographerAuth::class])->group(function () {
        Route::post('/portofolio', [PhotographerController::class, 'uploadPortofolio']);
        Route::get('/booking', [BookingController::class, 'getAllBookingPhotographer']);
        Route::get('/booking/{id}', [BookingController::class, 'getDetailBookingPhotographer']);
        Route::post('/booking/{id}', [BookingController::class, 'updateStatusBooking']);
    });

    // Only User can access
    Route::middleware(['auth:sanctum', UserAuth::class])->group(function () {
        Route::get('/booking', [BookingController::class, 'getAllBookingUser']);
        Route::get('/booking/{id}', [BookingController::class, 'getDetailBookingUser']);
        Route::post('/payment', [PaymentController::class, 'createPayment']);
        Route::get('/payment/{id}', [PaymentController::class, 'getTotalPrice']);
        Route::post('/booking', [BookingController::class, 'createBooking']);
        Route::get('/gallery/{user_id}', [GalleryController::class, 'getAllGallery']);
        Route::get('/gallery/{user_id}/{id}', [GalleryController::class, 'getDetailGallery']);
    });


    Route::get('/portofolio/{username}', [PortofolioController::class, 'getAllPortofolio']);
    Route::get('/portofolio/{username}/{id}', [PortofolioController::class, 'getDetailPortofolio']);

});
