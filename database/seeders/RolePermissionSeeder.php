<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        // Create base roles if missing
        Role::findOrCreate('admin', $guard);
        Role::findOrCreate('landlord', $guard);
        Role::findOrCreate('tenant', $guard);

        // Optional sample permissions (kept minimal)
        // You can expand these as needed.
        $manageBookings = Permission::findOrCreate('manage bookings', $guard);

        // Attach permission to admin & landlord by default (optional)
        $admin = Role::findByName('admin', $guard);
        $landlord = Role::findByName('landlord', $guard);

        if ($admin && ! $admin->hasPermissionTo($manageBookings->name)) {
            $admin->givePermissionTo($manageBookings);
        }
        if ($landlord && ! $landlord->hasPermissionTo($manageBookings->name)) {
            $landlord->givePermissionTo($manageBookings);
        }
    }
}
