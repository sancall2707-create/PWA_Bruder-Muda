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
        $superAdminPassword = env('DEV_SUPER_ADMIN_PASSWORD');
        if (empty($superAdminPassword)) {
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
        $superAdmin->community_location = 'Pusat Provinsi Semarang';
        $superAdmin->phone = '081234567890';
        $superAdmin->bio = 'Pengelola Utama Sistem Pembinaan Bruder Muda FIC Indonesia';
        $superAdmin->birth_date = '1980-01-15';
        $superAdmin->parents_name = 'Keluarga FIC';
        $superAdmin->last_education = 'S2 Manajemen Pendidikan';
        $superAdmin->entry_year = 2000;
        $superAdmin->save();

        // Admin
        $adminPassword = env('DEV_ADMIN_PASSWORD');
        if (empty($adminPassword)) {
            $adminPassword = 'Admin#FIC2026';
        }

        $admin = User::firstOrNew([
            'username' => 'admin',
        ]);
        $admin->name = 'Admin TPPBM';
        $admin->email = 'admin@fic.local';
        $admin->password = Hash::make($adminPassword);
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->community_location = 'Komunitas St. Joseph Semarang';
        $admin->phone = '081234567891';
        $admin->bio = 'Tim Pendamping Pembinaan Bruder Muda (TPPBM)';
        $admin->save();

        // User Demo 1
        $userPassword = env('DEV_USER_PASSWORD');
        if (empty($userPassword)) {
            $userPassword = 'User#FIC2026';
        }

        $user = User::firstOrNew([
            'username' => 'userdemo',
        ]);
        $user->name = 'Br. Yohanes FIC';
        $user->email = 'userdemo@fic.local';
        $user->password = Hash::make($userPassword);
        $user->role = 'user';
        $user->is_active = true;
        $user->community_location = 'Komunitas Bruderan Deltamas';
        $user->phone = '081234567892';
        $user->bio = 'Bruder Muda - Penugasan Unit Pendidikan Deltamas';
        $user->birth_date = '1998-05-20';
        $user->parents_name = 'Bpk. Yohanes & Ibu Maria';
        $user->last_education = 'S1 Filsafat Keagamaan';
        $user->entry_year = 2020;
        $user->save();

        // User Demo 2
        $user2 = User::firstOrNew([
            'username' => 'bruder_anton',
        ]);
        $user2->name = 'Br. Antonius FIC';
        $user2->email = 'anton@fic.local';
        $user2->password = Hash::make($userPassword);
        $user2->role = 'user';
        $user2->is_active = true;
        $user2->community_location = 'Komunitas Bruderan Yogyakarta';
        $user2->phone = '081234567893';
        $user2->bio = 'Bruder Muda - Penugasan Asrama Yogyakarta';
        $user2->save();
    }
}