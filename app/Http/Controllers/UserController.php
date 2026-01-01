<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Get authenticated user info
    public function me(Request $request)
    {
        $user = $request->user();

        $data = $this->formatUser($user);

        return response()->json($data);
    }

    // Update user profile (name, email)
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        $data = $this->formatUser($user);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $data,
        ]);
    }

    // Upload profile image
    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();

        try {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store image with unique filename
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->extension();
            $path = $file->storeAs('profile_images', $filename, 'public');

            $user->update(['profile_image' => $path]);

            $data = $this->formatUser($user->fresh());

            return response()->json([
                'message' => 'Profile image uploaded successfully',
                'user' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload profile image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Helper to format user data with token & profile image
    private function formatUser(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'profile_image' => $user->profile_image,
            'profile_image_url' => $user->profile_image ? asset("storage/{$user->profile_image}") : null,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'token' => $user->currentAccessToken()?->plainTextToken ?? null,
        ];
    }
}
