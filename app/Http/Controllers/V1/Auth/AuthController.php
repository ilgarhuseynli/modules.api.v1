<?php

namespace App\Http\Controllers\V1\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\ActiveDevice;
use App\Models\User;
use App\Services\ActiveDeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    protected $deviceService;

    public function __construct(ActiveDeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request)
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
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $name = $request->first_name.' '.$request->last_name;

        $keyword = Str::slug($name).' '.$request->email;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $name,
            'email' => $request->email,
            'keyword' => $keyword,
            'type' => UserType::CUSTOMER,
            'password' => Hash::make($request->string('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }



    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request)
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



    // get the authenticated user method
    public function user(Request $request) {

        $userData = new UserResource($request->user());

        return response()->json([
            $userData
        ]);
    }



    public function settings(Request $request) {

        $user = Auth::user();

//        $userPerms = $user->getPermissions();
        $userPerms = [];

        $permissionsArray = [];
        foreach ($userPerms as $key => $val) {
            $permissionsArray[$key] = $val['allow'];
        }

        return response()->json([
            'account' => new UserResource($user),
            'permissions' => $permissionsArray,
        ]);
    }

}
