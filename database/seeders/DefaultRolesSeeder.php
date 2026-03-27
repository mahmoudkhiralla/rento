<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DefaultRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure required roles exist for the 'web' guard
        foreach (['admin', 'landlord'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
