<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_number',
        'account_name',
        'password',
        'bank_name',
        'bank_logo',
        'account_type',
        'currency',
        'balance',
        'daily_limit',
        'phone_number',
        'email',
        'profile_image',
        'card_number',
        'expiry_date',
        'cvv',
        'api_token',
        'is_active',
    ];
}
