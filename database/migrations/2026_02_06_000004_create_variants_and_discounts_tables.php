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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('product_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('value', 10, 2);
            $table->boolean('is_percentage')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_description_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_description_lines');
        Schema::dropIfExists('product_discounts');
        Schema::dropIfExists('product_variants');
    }
};
