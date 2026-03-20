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
        Schema::create('temporary_orders', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('invoice_no')->unique();
            $blueprint->decimal('total_price', 15, 2);
            $blueprint->decimal('shipping_charged', 15, 2)->default(0);
            $blueprint->json('items_json'); // Store original items request
            $blueprint->string('shipping_address')->nullable();
            $blueprint->string('lat')->nullable();
            $blueprint->string('long')->nullable();
            $blueprint->string('phone_number')->nullable();
            $blueprint->string('method')->default('QR');
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_orders');
    }
};
