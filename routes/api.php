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
use App\Http\Controllers\UserController;



Route::middleware(['every-request'])->group(function (){

    // Authentication
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);

    // Only Authenticated User can access
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::get('/booking', [BookingController::class, 'getAllBooking']);
        Route::get('/user', [UserController::class, 'getProfileUser']);
        Route::put('/user', [UserController::class, 'updateProfileUser']);
        Route::post('/booking', [BookingController::class, 'createBooking']);
        Route::post('/payment/{booking_id}', [BookingController::class, 'payOrder']);
        Route::get('/preview/{booking_id}', [GalleryController::class, 'getPreviewGallery']);
        Route::get('/gallery/{booking_id}', [GalleryController::class, 'getDetailGallery']);
        Route::get('/gallery', [GalleryController::class, 'getAllGallery']);
        Route::delete('/gallery/{booking_id}', [GalleryController::class, 'deleteGallery']);
    });


    // Only Photographer can access
    Route::middleware(['auth:sanctum', FotographerAuth::class])->group(function () {
        Route::post('/portofolio', [PhotographerController::class, 'uploadPortofolio']);
        Route::get('/booking/{id}', [BookingController::class, 'getDetailBookingPhotographer']);
        Route::patch('/booking/{booking_id}', [BookingController::class, 'acceptOrder']);
        Route::delete('/booking/{booking_id}', [BookingController::class, 'rejectOrder']);
        Route::post('/gallery/{booking_id}', [GalleryController::class, 'uploadOrder']);
        Route::post('/preview/{booking_id}', [GalleryController::class, 'uploadPreview']);
    });

    // Only User can access
    Route::middleware(['auth:sanctum', UserAuth::class])->group(function () {
        Route::post('/photographer', [PhotographerController::class, 'register']);
    });

    Route::get('/getallphotographer', [PhotographerController::class, 'getAllPhotographer']);
    Route::get('/getphotographer/{id}', [PhotographerController::class, 'getDetailPhotographer']);
    Route::get('/portofolio/{id}', [PortofolioController::class, 'getAllPortofolio']);
    Route::get('/portofolio/{photographer_id}/{id}', [PortofolioController::class, 'getDetailPortofolio']);
    Route::get('/search', [PhotographerController::class, 'searchPhotographer']);
});
