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
        'name',
        'email',
        'password',
        'role',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'profile_image',
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

    public function getTokenAttribute()
    {
        // Only returns token if the user has created one
        return $this->currentAccessToken()?->plainTextToken ?? null;
    }
}
