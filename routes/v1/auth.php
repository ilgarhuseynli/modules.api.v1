<?php

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\DeviceController;
use App\Http\Controllers\V1\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\V1\Auth\NewPasswordController;
use App\Http\Controllers\V1\Auth\PasswordResetLinkController;
use App\Http\Controllers\V1\Auth\TwoFactorAuthController;
use App\Http\Controllers\V1\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

    Route::post('/reset-password', [NewPasswordController::class, 'store']);
});


Route::middleware('auth:sanctum')->group(function () {


    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['throttle:6,1']);


    Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'store'])
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('verification.verify');


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/authsettings', [AuthController::class, 'settings']);


    Route::middleware(['throttle:2fa'])->group(function () {

        Route::post('2fa/enable', [TwoFactorAuthController::class, 'enable']);

        Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify']);

        Route::post('2fa/disable', [TwoFactorAuthController::class, 'disable']);

        Route::post('2fa/validate', [TwoFactorAuthController::class, 'verify2FA']);

        Route::post('2fa/recovery-codes', [TwoFactorAuthController::class, 'generateRecoveryCodes']);

    });


    Route::get('devices', [DeviceController::class, 'index']);
    Route::post('devices/{device}/logout', [DeviceController::class, 'logout']);
    Route::post('devices/logout-all', [DeviceController::class, 'logoutAll']);

});
