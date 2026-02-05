<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update products with default data where missing
        DB::table('products')
            ->where('price', '=', 0)
            ->orWhereNull('price')
            ->update(['price' => 199.99]);

        DB::table('products')
            ->where('stock', '=', 0)
            ->orWhereNull('stock')
            ->update(['stock' => 50]);

        DB::table('products')
            ->whereNull('description')
            ->orWhere('description', '=', '')
            ->update([
                'description' => 'Experience premium comfort and style with our signature furniture collection. Crafted with high-quality materials to ensure durability and elegance for your home.'
            ]);

        DB::table('products')
            ->where('discount', '=', 0)
            ->orWhereNull('discount')
            ->update(['discount' => 10.00]); // Adding a small discount to show it works
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse for data seeding usually, or we could set them back to 0/null but that's destructive to purposeful edits.
    }
};
