<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Development seeder untuk membuat akun awal: Super Admin, Admin, dan User Demo.
     */
    public function run(): void
    {
        // Super Admin
        // Prioritaskan password dari environment variable DEV_SUPER_ADMIN_PASSWORD
        $superAdminPassword = env('DEV_SUPER_ADMIN_PASSWORD');
        if (empty($superAdminPassword)) {
            // LOCAL DEVELOPMENT ONLY
            // CHANGE BEFORE PRODUCTION
            $superAdminPassword = 'SuperAdmin#FIC2026';
        }

        $superAdmin = User::firstOrNew([
            'username' => 'superadmin',
        ]);
        $superAdmin->name = 'Super Admin FIC';
        $superAdmin->email = 'superadmin@fic.local';
        $superAdmin->password = Hash::make($superAdminPassword);
        $superAdmin->role = 'super_admin';
        $superAdmin->is_active = true;
        $superAdmin->save();

        // Admin
        // Prioritaskan password dari environment variable DEV_ADMIN_PASSWORD
        $adminPassword = env('DEV_ADMIN_PASSWORD');
        if (empty($adminPassword)) {
            // LOCAL DEVELOPMENT ONLY
            // CHANGE BEFORE PRODUCTION
            $adminPassword = 'Admin#FIC2026';
        }

        $admin = User::firstOrNew([
            'username' => 'admin',
        ]);
        $admin->name = 'Admin FIC';
        $admin->email = 'admin@fic.local';
        $admin->password = Hash::make($adminPassword);
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->save();

        // User Demo
        // Prioritaskan password dari environment variable DEV_USER_PASSWORD
        $userPassword = env('DEV_USER_PASSWORD');
        if (empty($userPassword)) {
            // LOCAL DEVELOPMENT ONLY
            // CHANGE BEFORE PRODUCTION
            $userPassword = 'User#FIC2026';
        }

        $user = User::firstOrNew([
            'username' => 'userdemo',
        ]);
        $user->name = 'User Demo FIC';
        $user->email = 'userdemo@fic.local';
        $user->password = Hash::make($userPassword);
        $user->role = 'user';
        $user->is_active = true;
        $user->save();
    }
}
