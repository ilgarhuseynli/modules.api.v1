<?php

use App\Http\Controllers\V1\UserPermissionsController;
use App\Http\Controllers\V1\UsersController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

    Route::post('roles/minlist', fn () => Role::select('id', 'title')->orderBy('title')->get());

    Route::group(['prefix' => 'users'], function () {
        Route::post('minlist', [UsersController::class, 'minlist']);
        Route::post('{user}/fileupload', [UsersController::class, 'fileupload']);
        Route::post('{user}/filedelete', [UsersController::class, 'filedelete']);
        Route::put('{user}/update-password', [UsersController::class, 'updatePassword']);

        Route::get('{user}/permissions', [UserPermissionsController::class, 'index'])->name('permissions.index');
        Route::put('{user}/permissions', [UserPermissionsController::class, 'update'])->name('permissions.update');

    });

    Route::apiResource('users', UsersController::class);

});
