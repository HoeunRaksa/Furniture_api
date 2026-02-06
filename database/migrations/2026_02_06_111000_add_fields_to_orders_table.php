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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->nullable()->after('total_price');
            $table->decimal('lat', 10, 8)->nullable()->after('shipping_address');
            $table->decimal('long', 11, 8)->nullable()->after('lat');
            $table->string('phone_number')->nullable()->after('long');
            $table->enum('method', ['QR', 'Cash'])->default('Cash')->after('phone_number');
            $table->string('shipping_status')->default('pending')->after('method');
            $table->string('payment_status')->default('pending')->after('shipping_status');
            $table->decimal('shipping_charged', 10, 2)->default(0)->after('payment_status');
            $table->string('invoice_no')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_address',
                'lat',
                'long',
                'phone_number',
                'method',
                'shipping_status',
                'payment_status',
                'shipping_charged',
                'invoice_no',
            ]);
        });
    }
};
