<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissionsFromPolicies extends Command
{
    protected $signature = 'permissions:sync 
                            {--clean : Remove permissions that no longer exist in any policy}';

    protected $description = 'Sync permissions from Policy classes. Permissions are derived from policy method names.';

    public function handle(): int
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard');
        $permissionsFromPolicies = $this->discoverPermissionsFromPolicies();
        $standalone = $this->getStandalonePermissions();

        $allPermissions = array_unique(array_merge($permissionsFromPolicies, $standalone));
        sort($allPermissions);

        $created = 0;
        foreach ($allPermissions as $name) {
            $perm = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guardName],
                ['name' => $name, 'guard_name' => $guardName]
            );
            if ($perm->wasRecentlyCreated) {
                $created++;
            }
        }

        if ($this->option('clean')) {
            $deleted = Permission::where('guard_name', $guardName)
                ->whereNotIn('name', $allPermissions)
                ->delete();
            $this->info("Removed {$deleted} obsolete permission(s).");
        }

        $this->info("Synced " . count($allPermissions) . " permission(s). Created {$created} new.");
        return Command::SUCCESS;
    }

    protected function discoverPermissionsFromPolicies(): array
    {
        $permissions = [];
        $policyPath = app_path('Policies');
        if (!is_dir($policyPath)) {
            return $permissions;
        }

        foreach (glob($policyPath . '/*.php') as $file) {
            $className = 'App\\Policies\\' . basename($file, '.php');
            if ($className === 'App\\Policies\\Concerns\\ChecksPermission') {
                continue;
            }
            if (!class_exists($className)) {
                continue;
            }
            $resource = $this->getResourceNameFromPolicy($className);
            if (!$resource) {
                continue;
            }
            $methods = $this->getPolicyMethods($className);
            foreach ($methods as $method) {
                $permissions[] = $resource . '.' . $method;
            }
        }

        return $permissions;
    }

    protected function getResourceNameFromPolicy(string $policyClass): ?string
    {
        $map = [
            'ProductPolicy' => 'products',
            'CategoryPolicy' => 'categories',
            'ExpensePolicy' => 'expenses',
            'JobCircularPolicy' => 'jobCirculars',
            'JobApplicationPolicy' => 'jobApplications',
            'WorkshopSeminarPolicy' => 'workshopSeminars',
            'WorkshopEnrollmentPolicy' => 'workshopEnrollments',
            'OrderPolicy' => 'orders',
            'UserPolicy' => 'users',
            'SponsorPolicy' => 'sponsors',
            'SponsorLevelPolicy' => 'sponsorLevels',
            'VideoPolicy' => 'videos',
            'WithdrawalPolicy' => 'withdrawals',
            'SettingPolicy' => 'settings',
            'DashboardPolicy' => 'dashboard',
            'ReportPolicy' => 'reports',
            'ProfilePolicy' => 'profile',
            'RolePolicy' => 'roles',
        ];
        $short = class_basename($policyClass);
        return $map[$short] ?? null;
    }

    protected function getPolicyMethods(string $policyClass): array
    {
        $methods = [];
        $ref = new \ReflectionClass($policyClass);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if ($name === '__construct') {
                continue;
            }
            if (str_starts_with($name, 'get') || str_starts_with($name, 'check')) {
                continue;
            }
            if ($method->getDeclaringClass()->getName() !== $policyClass) {
                continue;
            }
            $methods[] = $name;
        }
        return $methods;
    }

    protected function getStandalonePermissions(): array
    {
        return [
            'products.restore', 'categories.restore', 'orders.restore',
            'videos.restore', 'users.restore', 'sponsors.restore',
            'products.forceDelete', 'categories.forceDelete', 'orders.forceDelete',
            'videos.forceDelete', 'users.forceDelete', 'sponsors.forceDelete',
            'settings.smsSend',
        ];
    }
}
