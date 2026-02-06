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
            // Rename name column to username
            $table->renameColumn('name', 'username');

            // Drop extra profile fields
            $table->dropColumn([
                'prefix',
                'first_name',
                'last_name',
                'gender',
                'phone',
                'city',
                'address',
                'profile_completion'
            ]);
        });

        // Drop the duplicate username column if it exists from previous migration
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                // The renameColumn above already created a username from name
                // So we need to drop the old username column if it exists
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename username back to name
            $table->renameColumn('username', 'name');

            // Re-add the dropped columns
            $table->string('prefix', 10)->nullable()->after('id');
            $table->string('first_name')->nullable()->after('prefix');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('gender', 10)->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('gender');
            $table->string('city')->nullable()->after('phone');
            $table->text('address')->nullable()->after('city');
            $table->integer('profile_completion')->default(0)->after('address');
        });
    }
};
