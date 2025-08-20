<?php

use App\Http\Controllers\V1\CustomersController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::group(['prefix' => 'customers'], function () {
        Route::post('minlist', [CustomersController::class,'minlist']);
        Route::post('{user}/fileupload', [CustomersController::class,'fileupload']);
        Route::post('{user}/filedelete', [CustomersController::class,'filedelete']);
        Route::put('{user}/update-password', [CustomersController::class,'updatePassword']);
    });

    Route::apiResource('customers', CustomersController::class);

});
