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
        $products = DB::table('products')->get();

        $descriptions = [
            'Experience premium comfort and style with our signature furniture collection. Crafted with high-quality materials to ensure durability and elegance.',
            'Minimalist design meets maximum functionality. This piece is perfect for modern homes looking for a clean, sophisticated look.',
            'Add a touch of luxury to your living space. Featuring ergonomic design and premium finish, this item stands out in any room.',
            'Classic craftsmanship with a modern twist. Built to last and designed to impress, making it a perfect addition to your home.',
            'Sustainable, stylish, and sturdy. This eco-friendly furniture piece brings warmth and character to your interior decor.'
        ];

        foreach ($products as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'price' => rand(50, 800) + (rand(0, 99) / 100),
                    'stock' => rand(0, 150),
                    'discount' => rand(0, 10) > 7 ? rand(5, 30) : 0, // 30% chance of discount
                    'is_featured' => rand(0, 10) > 8, // 20% chance of being featured
                    'description' => $descriptions[array_rand($descriptions)],
                    'is_active' => true // Ensure all are active
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for randomization
    }
};
