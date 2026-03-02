<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB max
        ]);

        $existingUser = User::where('email', $request->email)->first();

        // If verified user exists → block
        if ($existingUser && $existingUser->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered. Please login instead.',
            ], 400);
        }

        // If unverified user exists → delete and re-register
        if ($existingUser && is_null($existingUser->email_verified_at)) {
            // Delete old profile image if exists
            if ($existingUser->profile_image && File::exists(public_path($existingUser->profile_image))) {
                File::delete(public_path($existingUser->profile_image));
            }
            $existingUser->delete();
            Log::info("Deleted unverified user: {$request->email}");
        }

        // Remove old pending entries and their images
        $oldPending = PendingUser::where('email', $request->email)->get();
        foreach ($oldPending as $old) {
            if ($old->profile_image && File::exists(public_path($old->profile_image))) {
                File::delete(public_path($old->profile_image));
            }
        }
        PendingUser::where('email', $request->email)->delete();

        $otp = random_int(100000, 999999);

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            try {
                // Create directory if not exists
                $uploadPath = public_path('uploads/profiles');
                if (! File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                // Generate unique filename
                $image = $request->file('profile_image');
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                // Move to public directory
                $image->move($uploadPath, $filename);
                $profileImagePath = 'uploads/profiles/'.$filename;
            } catch (\Exception $e) {
                Log::error('Failed to upload profile image during registration', [
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload profile image. Please try again.',
                ], 500);
            }
        }

        try {
            $pending = PendingUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile_image' => $profileImagePath,
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            if (! $this->sendOtpMail($request->email, $otp, 'Verify Your Account')) {
                // Delete uploaded image if email fails
                if ($profileImagePath && File::exists(public_path($profileImagePath))) {
                    File::delete(public_path($profileImagePath));
                }
                $pending->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully. Please check your email.',
                'email' => $request->email,
            ]);
        } catch (\Exception $e) {
            // Clean up profile image and database entry if it fails
            if ($profileImagePath && File::exists(public_path($profileImagePath))) {
                File::delete(public_path($profileImagePath));
            }
            PendingUser::where('email', $request->email)->delete();

            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (is_null($user->email_verified_at)) {
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Please verify your email with OTP first.',
            ], 403);
        }

        // Add full URL for profile image
        if ($user->profile_image) {
            $user->profile_image_url = url($user->profile_image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
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

        if (! $pending) {
            return response()->json([
                'success' => false,
                'message' => 'No pending verification found.',
            ], 404);
        }

        if (now()->greaterThan($pending->otp_expires_at)) {
            // Delete profile image if exists
            if ($pending->profile_image && File::exists(public_path($pending->profile_image))) {
                File::delete(public_path($pending->profile_image));
            }
            $pending->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP expired. Please request a new one.',
            ], 400);
        }

        if (! Hash::check($request->otp, $pending->otp_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please try again.',
            ], 400);
        }

        try {
            $user = User::create([
                'username' => $pending->name,
                'email' => $pending->email,
                'password' => $pending->password,
                'profile_image' => $pending->profile_image,
                'email_verified_at' => now(),
            ]);

            $pending->delete();

            // Add full URL for profile image
            if ($user->profile_image) {
                $user->profile_image_url = url($user->profile_image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Account verified successfully',
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ]);
        } catch (\Exception $e) {
            // Clean up if registration/verification fails
            if ($pending->profile_image && File::exists(public_path($pending->profile_image))) {
                File::delete(public_path($pending->profile_image));
            }
            $pending->delete();

            Log::error('Failed to verify OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * RESEND OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pending = PendingUser::where('email', $request->email)->first();

        if (! $pending) {
            $user = User::where('email', $request->email)
                ->whereNull('email_verified_at')
                ->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending verification found. Please register first.',
                ], 404);
            }

            $otp = random_int(100000, 999999);

            try {
                $pending = PendingUser::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'profile_image' => $user->profile_image,
                    'otp_hash' => Hash::make($otp),
                    'otp_expires_at' => now()->addMinutes(5),
                ]);

                if (! $this->sendOtpMail($user->email, $otp, 'Resend OTP')) {
                    $pending->delete();

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send OTP. Please try again.',
                    ], 500);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to resend OTP', [
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        $otp = random_int(100000, 999999);

        try {
            $pending->update([
                'otp_hash' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            if (! $this->sendOtpMail($pending->email, $otp, 'Resend OTP')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET AUTHENTICATED USER
     */
    public function user(Request $request)
    {
        $user = $request->user();

        // Add full URL for profile image
        if ($user->profile_image) {
            $user->profile_image_url = url($user->profile_image);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * UPDATE PROFILE
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Handle _method field from FormData
        if ($request->has('_method')) {
            $request->request->remove('_method');
        }

        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $oldImagePath = $user->profile_image;

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Create directory if not exists
                $uploadPath = public_path('uploads/profiles');
                if (! File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                // Generate unique filename
                $image = $request->file('profile_image');
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

                // Move to public directory
                $image->move($uploadPath, $filename);
                $validated['profile_image'] = 'uploads/profiles/'.$filename;

                // Delete old image
                if ($oldImagePath && File::exists(public_path($oldImagePath))) {
                    File::delete(public_path($oldImagePath));
                }
            }

            // Hash password if provided
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            // Add full URL for profile image
            if ($user->profile_image) {
                $user->profile_image_url = url($user->profile_image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            // Delete uploaded image if exists
            if (isset($validated['profile_image']) && File::exists(public_path($validated['profile_image']))) {
                File::delete(public_path($validated['profile_image']));
            }

            Log::error('Failed to update profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE PROFILE IMAGE
     */
    public function deleteProfileImage(Request $request)
    {
        try {
            $user = $request->user();
            $imagePath = $user->profile_image;

            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }

            $user->update(['profile_image' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Profile image deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete profile image', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * SEND OTP MAIL (helper)
     */
    private function sendOtpMail(string $email, int $otp, string $subject): bool
    {
        try {
            Mail::raw(
                "Your OTP is: $otp\n\nThis code will expire in 5 minutes.\n\nIf you didn't request this, please ignore this email.",
                fn ($msg) => $msg->to($email)->subject($subject)
            );

            return true;
        } catch (\Exception $e) {
            Log::error('OTP mail failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
