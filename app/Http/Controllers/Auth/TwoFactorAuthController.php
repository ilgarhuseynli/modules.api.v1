<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Hash;

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
        $user->two_factor_enabled = false; // Will be enabled after verification
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->save();

            return response()->json([
                'message' => '2FA has been enabled'
            ]);
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 400);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            $user->two_factor_enabled = false;
            $user->two_factor_secret = null;
            $user->save();

            return response()->json([
                'message' => '2FA has been disabled'
            ]);
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 400);
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();
        
        $valid = $google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if ($valid) {
            return response()->json([
                'message' => 'Code verified successfully'
            ]);
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 401);
    }
} 