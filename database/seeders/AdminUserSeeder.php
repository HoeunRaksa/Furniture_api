<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@furniture.api',
                'password' => Hash::make('123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
