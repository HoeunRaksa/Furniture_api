<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * REGISTER WITH OTP
     */
    public function register(Request $request)
    {
        // Cleanup expired OTPs
        PendingUser::where('otp_expires_at', '<', now())->delete();

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        // If verified user exists → block
        if ($existingUser && $existingUser->email_verified_at) {
            return response()->json([
                'message' => 'This email is already registered. Please login instead.'
            ], 400);
        }

        // If unverified user exists → delete and re-register
        if ($existingUser && is_null($existingUser->email_verified_at)) {
            $existingUser->delete();
            Log::info("Deleted unverified user: {$request->email}");
        }

        // Remove old pending entries
        PendingUser::where('email', $request->email)->delete();

        $otp = random_int(100000, 999999);

        $pending = PendingUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        if (!$this->sendOtpMail($request->email, $otp, 'Verify Your Account')) {
            $pending->delete();
            return response()->json([
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'email' => $request->email
        ]);
    }

    /**
     * LOGIN (BLOCK UNVERIFIED USERS)
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

        if (is_null($user->email_verified_at)) {
            Auth::logout();
            return response()->json([
                'message' => 'Please verify your email with OTP first.'
            ], 403);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }

    /**
     * VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pending_users,email',
            'otp' => 'required|digits:6',
        ]);

        $pending = PendingUser::where('email', $request->email)->first();

        if (now()->greaterThan($pending->otp_expires_at)) {
            $pending->delete();
            return response()->json([
                'message' => 'OTP expired. Please request a new one.'
            ], 400);
        }

        if (!Hash::check($request->otp, $pending->otp_hash)) {
            return response()->json([
                'message' => 'Invalid OTP. Please try again.'
            ], 400);
        }

        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        $pending->delete();

        return response()->json([
            'message' => 'Account verified successfully',
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }

    /**
     * RESEND OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pending = PendingUser::where('email', $request->email)->first();

        if (!$pending) {
            $user = User::where('email', $request->email)
                ->whereNull('email_verified_at')
                ->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No pending verification found. Please register first.'
                ], 404);
            }

            $otp = random_int(100000, 999999);

            $pending = PendingUser::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            $this->sendOtpMail($user->email, $otp, 'Resend OTP');

            return response()->json(['message' => 'OTP sent successfully']);
        }

        $otp = random_int(100000, 999999);

        $pending->update([
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        $this->sendOtpMail($pending->email, $otp, 'Resend OTP');

        return response()->json(['message' => 'OTP resent successfully']);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * SEND OTP MAIL (helper)
     */
    private function sendOtpMail(string $email, int $otp, string $subject): bool
    {
        try {
            Mail::raw(
                "Your OTP is $otp (expires in 5 minutes)",
                fn($msg) => $msg->to($email)->subject($subject)
            );
            return true;
        } catch (\Exception $e) {
            Log::error("OTP mail failed: " . $e->getMessage());
            return false;
        }
    }
}
