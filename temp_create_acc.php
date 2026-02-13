<?php
include "vendor/autoload.php";
$app = include "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Models\BankAccount;
use Illuminate\Support\Facades\Hash;

BankAccount::updateOrCreate(
    ['account_number' => '1122334455'],
    [
        'first_name' => 'Hoeun',
        'last_name' => 'Raksa',
        'password' => Hash::make('password123'),
        'balance' => 50000.00,
        'currency' => 'USD',
        'is_active' => 1,
    ]
);
echo "ACCOUNT_CREATED_SUCCESSFULLY\n";
