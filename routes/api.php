<?php


use App\Http\Controllers\V1\MediaController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    require __DIR__.'/v1/auth.php';

    require __DIR__.'/v1/user.php';



    Route::group(['middleware' => ['auth:sanctum', 'auth.gates']], function () {

        Route::post('media/store', [MediaController::class,'store']);

    });

});
