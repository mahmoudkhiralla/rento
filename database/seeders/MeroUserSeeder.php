<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MeroUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create or update the requested user
        $user = User::updateOrCreate(
            ['email' => 'meropubg250@gmail.com'],
            [
                'name' => 'Mero',
                'password' => Hash::make('mero1234'),
            ]
        );

        // Assign admin role (needed for accessing admin routes)
        if (! $user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }
    }
}
