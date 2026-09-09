<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'engineer']);

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@elitetechprecision.it'],
            [
                'name' => 'Awais Ho',
                'password' => bcrypt('admin123'),
                'status' => 'active',
            ]
        );
        $admin->syncRoles('admin');

        // Engineer user
        $engineer = User::firstOrCreate(
            ['email' => 'engineer@elitetechprecision.it'],
            [
                'name' => 'Engineer User',
                'password' => bcrypt('engineer123'),
                'status' => 'active',
            ]
        );
        $engineer->syncRoles('engineer');
    }
}
