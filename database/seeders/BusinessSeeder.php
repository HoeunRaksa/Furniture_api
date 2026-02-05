<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Codify',
                'currency' => 'USD',
                'currency_symbol' => '$',
                'timezone' => 'UTC',
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
                'email' => 'contact@furniture.api',
                'mobile' => '+855 000 000',
                'address' => 'Phnom Penh, Cambodia',
            ]
        );
    }
}
