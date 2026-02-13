<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BankUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BankAccount::updateOrCreate(
            ['account_number' => '1122334455'],
            [
                'first_name' => 'Hoeun',
                'last_name' => 'Raksa',
                'password' => Hash::make('password123'),
                'balance' => 50000.00,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );
    }
}
