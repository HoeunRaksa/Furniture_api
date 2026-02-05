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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 5)->default('$');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->string('tax_name', 50)->default('VAT');
            $table->boolean('tax_enabled')->default(false);
            $table->string('timezone')->default('UTC');
            $table->string('date_format', 20)->default('Y-m-d');
            $table->string('time_format', 20)->default('H:i');
            $table->text('footer_text')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('telegram')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
