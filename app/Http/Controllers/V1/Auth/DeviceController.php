<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devices = $request->user()
            ->activeDevices()
            ->orderBy('last_active_at', 'desc')
            ->get();

        return response()->json([
            'devices' => $devices
        ]);
    }

    public function logout(Request $request, $deviceId)
    {
        $device = $request->user()
            ->activeDevices()
            ->findOrFail($deviceId);

        // Revoke the token associated with this device
        $request->user()
            ->tokens()
            ->where('id', $device->token_id)
            ->delete();

        $device->delete();

        return response()->json([
            'message' => 'Device logged out successfully'
        ]);
    }

    public function logoutAll(Request $request)
    {
        // Keep current device token
        $currentTokenId = $request->user()->currentAccessToken()->id;

        // Delete all other tokens
        $request->user()
            ->tokens()
            ->where('id', '!=', $currentTokenId)
            ->delete();

        // Delete all other device records
        $request->user()
            ->activeDevices()
            ->where('token_id', '!=', $currentTokenId)
            ->delete();

        return response()->json([
            'message' => 'All other devices logged out successfully'
        ]);
    }
}
