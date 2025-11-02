<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth page
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                    'email_verified_at' => now(), // Auto-verify OAuth users
                ]);
            }

            // Log the user in
            Auth::login($user, true);

            // Determine redirect based on user role and KYC status
            // Admins go directly to dashboard
            if ($user->isAdmin()) {
                return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
            }
            
            // Regular users: check KYC status
            if (!$user->hasSubmittedKyc()) {
                return redirect()->route('kyc.create')->with('success', 'Welcome! Please complete your KYC application.');
            }
            
            if (!$user->isKycApproved()) {
                return redirect()->route('kyc.status')->with('info', 'Your KYC application is under review.');
            }
            
            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to login with Google. Please try again.');
        }
    }
}
