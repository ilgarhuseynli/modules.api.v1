<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActiveDevice;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AuthenticatedSessionController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
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
            'device_type' => $this->getDeviceType($agent),
            'ip_address' => $request->ip(),
            'location' => $this->getLocation($request->ip()),
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

    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isDesktop()) {
            return 'desktop';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isMobile()) {
            return 'mobile';
        }
        return 'unknown';
    }

    private function getLocation(string $ip): ?string
    {
        $location = $this->locationService->getLocation($ip);
        if ($location) {
            return implode(', ', array_filter([
                $location['city'],
                $location['region'],
                $location['country']
            ]));
        }
        return null;
    }
}
