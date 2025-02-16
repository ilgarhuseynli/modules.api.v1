<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActiveDevice;
use App\Services\ActiveDeviceService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AuthenticatedSessionController extends Controller
{
    protected $deviceService;

    public function __construct(ActiveDeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();
        $token = $user->createToken('auth-token');

        // Track the device
        $agent = new Agent();
        $device = new ActiveDevice([
            'user_id' => $user->id,
            'token_id' => $token->accessToken->id,
            'device_name' => $agent->device(),
            'device_type' => $this->deviceService->getDeviceType($agent),
            'ip_address' => $request->ip(),
            'location' => $this->deviceService->getLocation($request->ip()),
            'last_active_at' => now(),
        ]);
        $device->save();

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user,
            'requires_2fa' => $user->two_factor_enabled,
            'current_device' => $device
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Delete the device record
        $tokenId = $request->user()->currentAccessToken()->id;
        ActiveDevice::where('token_id', $tokenId)->delete();

        // Delete the token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}
