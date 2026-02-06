<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = ['description', 'price', 'discount', 'stock', 'is_featured', 'slug', 'is_recommended', 'active'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('products', $col)) {
                    // $table->dropColumn($col); // SQLite index issues
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('slug')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->boolean('active')->default(true);
        });
    }
};
