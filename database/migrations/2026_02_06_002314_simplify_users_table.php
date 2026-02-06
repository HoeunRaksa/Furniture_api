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
        // First, drop the old username column if it exists from previous migration
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                if (config('database.default') === 'sqlite') {
                    $table->dropUnique('users_username_unique');
                } else {
                    $table->dropUnique(['username']);
                }
                $table->dropColumn('username');
            });
        }

        // Drop extra profile fields
        Schema::table('users', function (Blueprint $table) {
            $columns = ['prefix', 'first_name', 'last_name', 'gender', 'phone', 'city', 'address', 'profile_completion'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Finally, rename name column to username
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'username');
        });
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
