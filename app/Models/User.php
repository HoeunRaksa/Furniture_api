<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @method \Laravel\Sanctum\NewAccessToken createToken(string $name, array $abilities = ['*'])
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Add 'role', 'otp', and 'otp_expires_at' to fillable
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'otp',
    'otp_expires_at',
    'email_verified_at',
];

    protected $hidden = [
        'password',
        'remember_token',
        'otp', // hide OTP for serialization
    ];
protected $casts = [
    'otp_expires_at' => 'datetime',
    'email_verified_at' => 'datetime',
];
}
