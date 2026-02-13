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
            ['name' => 'create_users', 'label' => 'Create Users', 'group' => 'Users'],
            ['name' => 'edit_users', 'label' => 'Edit Users', 'group' => 'Users'],
            ['name' => 'delete_users', 'label' => 'Delete Users', 'group' => 'Users'],

            // Roles
            ['name' => 'view_roles', 'label' => 'View Roles', 'group' => 'Roles'],
            ['name' => 'manage_roles', 'label' => 'Manage Roles', 'group' => 'Roles'],

            // Business
            ['name' => 'view_business', 'label' => 'View Business Settings', 'group' => 'Business'],
            ['name' => 'manage_business', 'label' => 'Manage Business Settings', 'group' => 'Business'],
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
        // Staff should generally only view, not manage these core admin areas
        $sensitive = [
            'manage_settings', // Legacy
            'manage_business',
            'manage_roles',
            'create_users',
            'edit_users',
            'delete_users',
            'delete_products',
            'manage_categories',
            'manage_orders',
        ];
        $ids = Permission::whereIn('name', $sensitive)->pluck('id');
        RolePermission::where('role', 'staff')->whereIn('permission_id', $ids)->delete();
    }

    private function assignToRole($role, $permission)
    {
        RolePermission::firstOrCreate([
            'role' => $role,
            'permission_id' => $permission->id,
        ]);
    }
}
