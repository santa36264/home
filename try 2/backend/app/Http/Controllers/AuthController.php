<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // Log login activity
        ActivityLog::log('login', "User {$user->name} logged in");

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        // Check if public registration is allowed
        if (SystemSetting::isAdminOnlyRegistration()) {
            return response()->json([
                'message' => 'Public registration is disabled. Please contact an administrator.'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'created_by_admin' => false,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Log registration activity
        ActivityLog::log('register', "User {$user->name} registered");

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        // Log logout activity
        ActivityLog::log('logout', "User {$request->user()->name} logged out");
        
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function checkRegistrationMode()
    {
        return response()->json([
            'registration_mode' => SystemSetting::get('registration_mode', 'public'),
            'is_public' => SystemSetting::isPublicRegistration(),
        ]);
    }
}
