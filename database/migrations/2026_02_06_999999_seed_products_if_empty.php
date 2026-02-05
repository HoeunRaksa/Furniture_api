<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safety check: Only seed if products table is empty
        if (Schema::hasTable('products') && Product::count() == 0) {

            // Ensure a category exists
            $category = Category::firstOrCreate(['name' => 'General'], [
                'description' => 'Default Category',
                'is_active' => true
            ]);

            $products = [
                ['name' => 'Classic Leather Sofa', 'price' => 1200.00, 'stock' => 10, 'is_active' => true],
                ['name' => 'Modern Coffee Table', 'price' => 450.00, 'stock' => 25, 'is_active' => true],
                ['name' => 'Ergonomic Office Chair', 'price' => 299.99, 'stock' => 50, 'is_active' => true],
                ['name' => 'Vintage Wooden Desk', 'price' => 850.00, 'stock' => 5, 'is_active' => true],
                ['name' => 'Minimalist Floor Lamp', 'price' => 120.00, 'stock' => 100, 'is_active' => true],
                ['name' => 'Velvet Dining Chair', 'price' => 180.00, 'stock' => 40, 'is_active' => true],
                ['name' => 'Abstract Wall Art', 'price' => 95.00, 'stock' => 15, 'is_active' => true],
                ['name' => 'King Size Bed Frame', 'price' => 1500.00, 'stock' => 8, 'is_active' => true],
                ['name' => 'Ceramic Flower Vase', 'price' => 45.00, 'stock' => 200, 'is_active' => true],
                ['name' => 'Bookshelf Unit', 'price' => 320.00, 'stock' => 12, 'is_active' => true],
            ];

            foreach ($products as $p) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $p['name'],
                    'description' => 'A high-quality ' . $p['name'] . ' for your home or office.',
                    'price' => $p['price'],
                    'discount' => 0,
                    'stock' => $p['stock'],
                    'is_featured' => rand(0, 1) == 1,
                    'is_active' => true,
                    'active' => true
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed for seeding
    }
};
