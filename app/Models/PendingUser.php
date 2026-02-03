<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp_hash',
        'otp_expires_at',
    ];
}
