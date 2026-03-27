<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SyncUserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        // Ensure base roles exist for the configured guard
        Role::findOrCreate('admin', $guard);
        Role::findOrCreate('landlord', $guard);
        Role::findOrCreate('tenant', $guard);

        User::query()
            ->select(['id', 'user_type'])
            ->orderBy('id')
            ->chunk(200, function ($users) use ($guard) {
                foreach ($users as $user) {
                    $rolesToAssign = [];
                    $type = $user->user_type ?? null;

                    if (in_array($type, ['landlord', 'both'])) {
                        $rolesToAssign[] = 'landlord';
                    }
                    if (in_array($type, ['tenant', 'both'])) {
                        $rolesToAssign[] = 'tenant';
                    }

                    // Assign roles without removing any existing ones
                    foreach (array_unique($rolesToAssign) as $roleName) {
                        try {
                            // Only assign if not already assigned
                            if (method_exists($user, 'hasRole') && ! $user->hasRole($roleName)) {
                                $user->assignRole(Role::findOrCreate($roleName, $guard));
                            }
                        } catch (\Throwable $e) {
                            // Skip problematic users to avoid halting the seeder
                        }
                    }
                }
            });
    }
}
