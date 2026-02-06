<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Inventory
            ['name' => 'view_products', 'label' => 'View Products', 'group' => 'Inventory'],
            ['name' => 'create_products', 'label' => 'Create Products', 'group' => 'Inventory'],
            ['name' => 'edit_products', 'label' => 'Edit Products', 'group' => 'Inventory'],
            ['name' => 'delete_products', 'label' => 'Delete Products', 'group' => 'Inventory'],
            ['name' => 'view_categories', 'label' => 'View Categories', 'group' => 'Inventory'],
            ['name' => 'manage_categories', 'label' => 'Manage Categories', 'group' => 'Inventory'],

            // Orders
            ['name' => 'view_orders', 'label' => 'View Orders', 'group' => 'Sales'],
            ['name' => 'manage_orders', 'label' => 'Manage Orders', 'group' => 'Sales'],

            // Users
            ['name' => 'view_users', 'label' => 'View Users', 'group' => 'Users'],
            ['name' => 'manage_users', 'label' => 'Manage Users', 'group' => 'Users'],

            // Settings
            ['name' => 'manage_settings', 'label' => 'Manage Settings', 'group' => 'Settings'],
        ];

        foreach ($permissions as $perm) {
            $p = Permission::firstOrCreate(['name' => $perm['name']], $perm);

            // Default Assignments
            // Admin gets everything via User model check, but let's be explicit if we change logic later
            // Staff gets Inventory & Orders
            $this->assignToRole('staff', $p);

            // User gets nothing by default in this panel
        }

        // Remove sensitive perms from staff
        $sensitive = ['manage_users', 'manage_settings', 'delete_products'];
        $ids = Permission::whereIn('name', $sensitive)->pluck('id');
        RolePermission::where('role', 'staff')->whereIn('permission_id', $ids)->delete();
    }

    private function assignToRole($role, $permission)
    {
        RolePermission::firstOrCreate([
            'role' => $role,
            'permission_id' => $permission->id
        ]);
    }
}
