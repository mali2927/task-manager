<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'workspace.invite-members',
            'workspace.remove-members',
            'workspace.update-roles',
            'workspace.manage-teams',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $ownerRole = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $memberRole = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $guestRole = Role::firstOrCreate(['name' => 'Guest', 'guard_name' => 'web']);

        // Give Owner and Admin full workspace member management permissions in DB table
        $ownerRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);

        // Explicitly ensure Member and Guest have NO member management permissions
        $memberRole->syncPermissions([]);
        $guestRole->syncPermissions([]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'workspace.invite-members',
            'workspace.remove-members',
            'workspace.update-roles',
            'workspace.manage-teams',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
