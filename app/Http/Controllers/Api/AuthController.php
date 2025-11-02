<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'authtoken' => $token,
            'userid' => $user->id,
            'RegistrationFlag' => $user->from_register ? 1 : 0,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'kyc_status' => $user->kyc_status,
                    'from_register' => $user->from_register,
                ],
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }

    /**
     * Get user details for profile
     */
    public function userDetails(Request $request)
    {
        $user = $request->user();
        
        // Get user detail information if available
        $userDetail = $user->userDetail;
        
        return response()->json([
            'success' => true,
            'message' => 'User details retrieved successfully',
            'data' => [
                'full_name' => $userDetail ? $userDetail->name : $user->name,
                'email' => $user->email,
                'phone' => $userDetail ? $userDetail->mobile : null,
                'createat' => $user->created_at->format('Y-m-d H:i:s')
            ]
        ], 200);
    }

    /**
     * Google OAuth Login (Simplified - Direct Data)
     */
    public function googleLoginSimple(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'google_id' => 'required|string',
            'image_url' => 'nullable|url',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            // Find or create user
            $user = User::Where('email', $request->email)
                        ->first();

            if ($user) {
                // Update existing user with latest Google info
                $user->update([
                    'google_id' => $request->google_id,
                    'avatar' => $request->image_url,
                    'name' => $request->name ?? $user->name,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $request->name ?? 'Google User',
                    'email' => $request->email,
                    'google_id' => $request->google_id,
                    'avatar' => $request->image_url,
                    'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                    'email_verified_at' => now(), // Auto-verify OAuth users
                    'role' => 'user',
                    'kyc_status' => 'not_submitted',
                    'from_register' => 0, // Google users start with 0
                ]);
            }

            // Create API token
            $token = $user->createToken('google_auth_token')->plainTextToken;

            // Check if user has completed KYC (user details)
            $hasUserDetails = $user->userDetail ? 1 : 0;

            return response()->json([
                'success' => true,
                'message' => 'Google login successful',
                'userid' => $user->id,
                'authtoken' => $token,
                'kyc_completed' => $hasUserDetails,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google login failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check KYC completion status
     */
    public function kycStatus(Request $request)
    {
        $user = $request->user();
        
        // Check if user has completed KYC (user details)
        $hasUserDetails = $user->userDetail ? 1 : 0;
        
        return response()->json([
            'success' => true,
            'message' => 'KYC status retrieved successfully',
            'data' => [
                'user_id' => $user->id,
                'kyc_completed' => $hasUserDetails,
                'has_user_details' => $hasUserDetails ? 'Yes' : 'No',
                'user_details_available' => $hasUserDetails ? true : false
            ]
        ], 200);
    }
}
