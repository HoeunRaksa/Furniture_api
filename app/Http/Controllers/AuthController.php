<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * REGISTER WITH OTP
     * ✅ Now handles re-registration for unverified users
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Check if user already exists
        $existingUser = User::where('email', $request->email)->first();
        
        if ($existingUser) {
            // ✅ If user exists but NOT verified, allow re-registration
            if (is_null($existingUser->email_verified_at)) {
                // Delete the unverified user to start fresh
                $existingUser->delete();
                
                // Continue with registration flow below...
                \Log::info("Deleted unverified user and allowing re-registration: {$request->email}");
            } else {
                // User exists and IS verified - block registration
                return response()->json([
                    'message' => 'This email is already registered. Please login instead.'
                ], 400);
            }
        }

        // Delete any existing pending user with this email
        PendingUser::where('email', $request->email)->delete();

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Create pending user
        PendingUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Send OTP email
        try {
            Mail::raw("Your OTP is $otp (expires in 5 minutes)", function ($msg) use ($request) {
                $msg->to($request->email)
                    ->subject('Verify Your Account');
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP email: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'email' => $request->email
        ], 200);
    }

    /**
     * LOGIN (BLOCKED UNTIL VERIFIED)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Block login if email not verified
        if (is_null($user->email_verified_at)) {
            Auth::logout();
            return response()->json([
                'message' => 'Please verify your email with OTP first.'
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * VERIFY OTP
     * ✅ Fixed validation to check pending_users table correctly
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pending_users,email', // ✅ This now works correctly
            'otp' => 'required|digits:6',
        ]);

        $pending = PendingUser::where('email', $request->email)->first();

        if (!$pending) {
            return response()->json([
                'message' => 'No pending verification found for this email. Please register again.'
            ], 404);
        }

        // Check if OTP expired
        if (now()->greaterThan($pending->otp_expires_at)) {
            $pending->delete();
            return response()->json(['message' => 'OTP expired. Please request a new one.'], 400);
        }

        // Validate OTP
        if (!Hash::check($request->otp, $pending->otp_hash)) {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 400);
        }

        // Create real user (verified)
        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        // Delete pending user
        $pending->delete();

        // Generate token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Account verified successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * RESEND OTP
     * ✅ Now checks pending_users table and handles edge cases
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if there's a pending user
        $pending = PendingUser::where('email', $request->email)->first();

        if (!$pending) {
            // Check if user exists but not verified
            $user = User::where('email', $request->email)
                         ->whereNull('email_verified_at')
                         ->first();

            if ($user) {
                // Create pending user entry for unverified user
                $otp = random_int(100000, 999999);

                $pending = PendingUser::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'otp_hash' => Hash::make($otp),
                    'otp_expires_at' => now()->addMinutes(5),
                ]);

                // Send OTP
                try {
                    Mail::raw("Your OTP is $otp (expires in 5 minutes)", function ($msg) use ($user) {
                        $msg->to($user->email)->subject('Resend OTP');
                    });
                } catch (\Exception $e) {
                    \Log::error("Failed to send OTP email: " . $e->getMessage());
                }

                return response()->json([
                    'message' => 'OTP sent successfully'
                ]);
            }

            // No pending user and no unverified user found
            return response()->json([
                'message' => 'No pending verification found. Please register first.'
            ], 404);
        }

        // Generate new OTP
        $otp = random_int(100000, 999999);

        // Update pending user
        $pending->update([
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Send OTP email
        try {
            Mail::raw("Your new OTP is $otp (expires in 5 minutes)", function ($msg) use ($pending) {
                $msg->to($pending->email)->subject('Resend OTP');
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP email: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'OTP resent successfully'
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}