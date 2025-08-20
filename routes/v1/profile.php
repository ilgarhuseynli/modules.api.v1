<?php

use App\Http\Controllers\V1\ProfileController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::group(['prefix' => 'profile'], function () {
        Route::get('info', [ProfileController::class,'index']);
        Route::put('/', [ProfileController::class,'update']);
        Route::delete('/', [ProfileController::class,'destroy']);

        Route::post('fileupload', [ProfileController::class,'fileupload']);
        Route::post('filedelete', [ProfileController::class,'filedelete']);
        Route::put('update-password', [ProfileController::class,'updatePassword']);
    });
});
