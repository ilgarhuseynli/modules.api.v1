<?php

use App\Http\Controllers\V1\UsersController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::group(['prefix' => 'users'], function () {
        Route::post('{user}/fileupload', [UsersController::class,'fileupload']);
        Route::post('{user}/filedelete', [UsersController::class,'filedelete']);
        Route::post('{user}/update-password', [UsersController::class,'updatePassword']);
    });

    Route::apiResource('users', UsersController::class);

});
