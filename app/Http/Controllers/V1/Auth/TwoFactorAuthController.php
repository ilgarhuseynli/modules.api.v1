<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthController extends Controller
{
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json([
                'message' => '2FA is already enabled'
            ], 400);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_enabled = false;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'recovery_codes' => $recoveryCodes
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'remember_device' => 'boolean'
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->two_factor_verified_at = now();
            $user->save();

            if ($request->remember_device) {
                $deviceId = Str::random(40);
                $user->addTrustedDevice($deviceId);

                return response()->json([
                    'message' => '2FA has been enabled',
                    'device_id' => $deviceId
                ]);
            }

            return response()->json([
                'message' => '2FA has been enabled'
            ]);
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 400);
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'device_id' => 'string|nullable',
            'remember_device' => 'boolean'
        ]);

        $user = $request->user();

        // Check if device is trusted
        if ($request->device_id && $user->isTrustedDevice($request->device_id)) {
            return response()->json([
                'message' => 'Device is trusted'
            ]);
        }

        // Try recovery code
        if (strlen($request->code) === 10 && $user->validateRecoveryCode($request->code)) {
            if ($request->remember_device) {
                $deviceId = Str::random(40);
                $user->addTrustedDevice($deviceId);

                return response()->json([
                    'message' => 'Recovery code accepted',
                    'device_id' => $deviceId
                ]);
            }

            return response()->json([
                'message' => 'Recovery code accepted'
            ]);
        }

        // Verify 2FA code
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            if ($request->remember_device) {
                $deviceId = Str::random(40);
                $user->addTrustedDevice($deviceId);

                return response()->json([
                    'message' => 'Code verified successfully',
                    'device_id' => $deviceId
                ]);
            }

            return response()->json([
                'message' => 'Code verified successfully'
            ]);
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 401);
    }

    public function generateRecoveryCodes(Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return response()->json([
                'message' => '2FA is not enabled'
            ], 400);
        }

        $recoveryCodes = $user->generateRecoveryCodes();

        return response()->json([
            'recovery_codes' => $recoveryCodes
        ]);
    }
}
