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
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users|unique:pending_users',
            'password' => 'required|string|min:6',
        ]);

        $otp = random_int(100000, 999999);

        PendingUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Your OTP is $otp (expires in 5 minutes)", function ($msg) use ($request) {
            $msg->to($request->email)
                ->subject('Verify OTP');
        });

        return response()->json([
            'message' => 'OTP sent successfully'
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
            return response()->json([
                'message' => 'Please verify OTP first.',
                'user_id' => $user->id
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
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pending_users,email',
            'otp' => 'required|digits:6',
        ]);

        $pending = PendingUser::where('email', $request->email)->first();

        // Expired OTP
        if (now()->greaterThan($pending->otp_expires_at)) {
            $pending->delete();
            return response()->json(['message' => 'OTP expired'], 400);
        }

        // Invalid OTP
        if (!Hash::check($request->otp, $pending->otp_hash)) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // Create real user
        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        $pending->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Account verified',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * RESEND OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:pending_users,email',
        ]);

        $pending = PendingUser::where('email', $request->email)->first();

        $otp = random_int(100000, 999999);

        $pending->update([
            'otp_hash' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Your new OTP is $otp (expires in 5 minutes)", function ($msg) use ($pending) {
            $msg->to($pending->email)->subject('Resend OTP');
        });

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

        return response()->json(['message' => 'Logged out']);
    }
}
