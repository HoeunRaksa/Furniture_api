<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'profile_image',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Add this accessor to include token in JSON
    protected $appends = ['token'];

    public function getAvatarUrlAttribute()
    {
        return $this->profile_image
            ? asset($this->profile_image)
            : asset('images/default-avatar.png');
    }

    public function getTokenAttribute()
    {
        // Only returns token if the user has created one
        return $this->currentAccessToken()?->plainTextToken ?? null;
    }

    /**
     * Check if user has a specific permission via their role.
     */
    public function hasPermission($permissionName)
    {
        if ($this->role === 'admin') {
            return true;
        } // Super Admin

        return \App\Models\RolePermission::where('role', $this->role)
            ->whereHas('permission', function ($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })->exists();
    }
}
