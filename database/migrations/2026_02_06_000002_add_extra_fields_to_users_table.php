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
        Schema::table('users', function (Blueprint $table) {
            $table->string('prefix', 10)->nullable()->after('id');
            $table->string('first_name')->nullable()->after('prefix');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->nullable()->unique()->after('last_name');
            $table->string('gender', 10)->nullable()->after('username');
            $table->boolean('is_active')->default(true)->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prefix', 'first_name', 'last_name', 'username', 'gender', 'is_active']);
        });
    }
};
