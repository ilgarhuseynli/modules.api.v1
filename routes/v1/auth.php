<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\Auth\DeviceController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('api.register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('api.login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('api.password.email');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('api.verification.send');

Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'store'])
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('api.verification.verify');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('api.logout');

Route::middleware(['auth:sanctum', 'throttle:2fa'])->group(function () {
    Route::post('2fa/enable', [TwoFactorAuthController::class, 'enable']);
    Route::post('2fa/verify', [TwoFactorAuthController::class, 'verify']);
    Route::post('2fa/disable', [TwoFactorAuthController::class, 'disable']);
    Route::post('2fa/validate', [TwoFactorAuthController::class, 'verify2FA']);
    Route::post('2fa/recovery-codes', [TwoFactorAuthController::class, 'generateRecoveryCodes']);
});

Route::get('devices', [DeviceController::class, 'index']);
Route::post('devices/{device}/logout', [DeviceController::class, 'logout']);
Route::post('devices/logout-all', [DeviceController::class, 'logoutAll']); 