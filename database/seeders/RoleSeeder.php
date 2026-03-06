<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->call('permissions:sync');

        $guard = config('auth.defaults.guard');

        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => $guard],
            ['name' => 'Super Admin', 'guard_name' => $guard]
        );
        $superAdmin->syncPermissions(
            \Spatie\Permission\Models\Permission::where('guard_name', $guard)->pluck('name')
        );

        // Assign Super Admin to all existing admin users who have no role
        User::where('role', 'admin')->each(function (User $user) use ($superAdmin) {
            if ($user->roles()->count() === 0) {
                $user->assignRole($superAdmin);
            }
        });

        $this->command->info('Super Admin role created and assigned to admin users.');
    }
}
