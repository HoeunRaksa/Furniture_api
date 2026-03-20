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
        Schema::table('temporary_orders', function (Blueprint $table) {
            $table->string('tran_id')->unique()->after('items_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_orders', function (Blueprint $table) {
            $table->dropColumn('tran_id');
        });
    }
};
