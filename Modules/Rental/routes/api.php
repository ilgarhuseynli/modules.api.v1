<?php

use Illuminate\Support\Facades\Route;
use Modules\Rental\Controllers\BadgeController;
use Modules\Rental\Controllers\BookingController;
use Modules\Rental\Controllers\CarCategoryController;
use Modules\Rental\Controllers\CarController;
use Modules\Rental\Controllers\ExtraController;
use Modules\Rental\Controllers\LocationController;

Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::group(['prefix' => 'rental'], function () {

        // Car Categories
        Route::post('car-categories/minlist', [CarCategoryController::class, 'minlist']);
        Route::apiResource('car-categories', CarCategoryController::class)->parameters(['car-categories' => 'carCategory']);

        // Badges
        Route::post('badges/minlist', [BadgeController::class, 'minlist']);
        Route::apiResource('badges', BadgeController::class);

        // Cars
        Route::post('cars/minlist', [CarController::class, 'minlist']);
        Route::post('cars/{car}/fileupload', [CarController::class, 'fileupload']);
        Route::post('cars/{car}/filedelete', [CarController::class, 'filedelete']);
        Route::apiResource('cars', CarController::class);

        // Extras
        Route::post('extras/minlist', [ExtraController::class, 'minlist']);
        Route::apiResource('extras', ExtraController::class);

        // Locations
        Route::post('locations/minlist', [LocationController::class, 'minlist']);
        Route::apiResource('locations', LocationController::class);

        // Bookings
        Route::apiResource('bookings', BookingController::class);
    });

});
