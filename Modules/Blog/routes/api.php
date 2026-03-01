<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\CategoryController;
use Modules\Blog\Controllers\PostController;

Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::group(['prefix' => 'blog'], function () {

        // Categories
        Route::apiResource('categories', CategoryController::class);

        // Posts
        Route::post('posts/{post}/fileupload', [PostController::class, 'fileupload']);
        Route::post('posts/{post}/filedelete', [PostController::class, 'filedelete']);
        Route::apiResource('posts', PostController::class);
    });

});
