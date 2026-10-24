<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed seluruh akun resmi FIC: Super Admin, TPPBM, Admin, dan Seluruh Bruder Muda.
     */
    public function run(): void
    {
        $superAdminPassword = env('DEV_SUPER_ADMIN_PASSWORD', 'SuperAdmin#FIC2026');
        $adminPassword = env('DEV_ADMIN_PASSWORD', 'Admin#FIC2026');
        $ketuaPassword = env('DEV_KETUA_PASSWORD', 'Ketua#FIC2026');
        $userPassword = env('DEV_USER_PASSWORD', 'User#FIC2026');
        $defaultPassword = Hash::make('123456');

        // 1. Akun Dasar Sistem
        $baseUsers = [
            [
                'username' => 'superadmin',
                'name' => 'Bruder Andrias Purwanto, FIC-Cah Ndeso',
                'email' => 'superadmin@fic.local',
                'password' => Hash::make($superAdminPassword),
                'role' => 'super_admin',
                'community_location' => 'Pusat Provinsi Semarang',
                'phone' => '081234567890',
                'bio' => 'Pengelola Utama Sistem Pembinaan Bruder Muda FIC Indonesia',
            ],
            [
                'username' => 'sarwoyes',
                'name' => 'Br. Sarwo Ono, FIC',
                'email' => 'sarwoyes@fic.local',
                'password' => Hash::make($ketuaPassword),
                'role' => 'admin_ketua_tppbm',
                'community_location' => 'Semarang',
                'phone' => '081234567899',
                'bio' => 'Ketua Tim Pemantap Panggilan Bruder Muda (TPPBM)',
            ],
            [
                'username' => 'galihsih',
                'name' => 'Br. F.A. Galih Sih Hartanta, FIC',
                'email' => 'fagsh08@gmail.com',
                'password' => Hash::make($ketuaPassword),
                'role' => 'admin_ketua_tppbm',
                'community_location' => 'Muntilan',
                'phone' => '087832139199',
                'bio' => 'Ketua Tim Pemantap Panggilan Bruder Muda (TPPBM)',
            ],
            [
                'username' => 'admin',
                'name' => 'Admin TPPBM',
                'email' => 'admin@fic.local',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'community_location' => 'Komunitas St. Joseph Semarang',
                'phone' => '081234567891',
                'bio' => 'Staf Tim Pendamping Pembinaan Bruder Muda (TPPBM)',
            ],
            [
                'username' => 'userdemo',
                'name' => 'Br. Yohanes FIC',
                'email' => 'userdemo@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Komunitas Bruderan Deltamas',
                'phone' => '081234567892',
                'bio' => 'Bruder Muda - Penugasan Unit Pendidikan Deltamas',
            ],
            [
                'username' => 'bruder_anton',
                'name' => 'Br. Antonius FIC',
                'email' => 'anton@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Komunitas Bruderan Yogyakarta',
                'phone' => '081234567893',
            ],
            [
                'username' => 'bruder_michael',
                'name' => 'Br. Michael FIC',
                'email' => 'michael@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Komunitas Semarang',
                'phone' => '081299998888',
            ],
            [
                'username' => 'brunofic',
                'name' => 'Br. Yohanes Sarwono, FIC',
                'email' => 'brunofic@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Jakarta',
                'phone' => '021223254',
            ],
            [
                'username' => 'poerw',
                'name' => 'Br. Purwanto, FIC',
                'email' => 'poerw@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Semarang',
            ],
            [
                'username' => 'agsuparno',
                'name' => 'Br. Agustinus Suparno, FIC',
                'email' => 'agsuparno@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Yogyakarta',
            ],
            [
                'username' => 'ysapto_ficindo',
                'name' => 'Br. Yohanes Sapto Nugroho, FIC',
                'email' => 'ysapto@fic.local',
                'password' => Hash::make($userPassword),
                'role' => 'user',
                'community_location' => 'Surakarta',
            ],
        ];

        foreach ($baseUsers as $u) {
            $user = User::firstOrNew(['username' => $u['username']]);
            $user->fill($u);
            if (! $user->exists || empty($user->password)) {
                $user->password = $u['password'];
            }
            $user->is_active = true;
            $user->save();
        }

        // 2. Daftar 25 Akun Resmi Pembinaan (5 Admin + 20 Bruder Muda)
        $officialAccounts = [
            // Admin (1 - 5)
            [
                'name' => 'Br. Agustinus Giwal Santoso, FIC',
                'role' => 'admin',
                'username' => 'giwalfic',
                'email' => 'giwalfic@fic.local',
                'community_location' => 'Semarang',
            ],
            [
                'name' => 'Br. Albertus Suwarto, FIC',
                'role' => 'admin',
                'username' => 'wartofic',
                'email' => 'wartofic@fic.local',
                'community_location' => 'Semarang',
            ],
            [
                'name' => 'Br. FA. Galih Sih Hartanta, FIC',
                'role' => 'admin_ketua_tppbm',
                'username' => 'galihfic',
                'email' => 'galihfic@fic.local',
                'community_location' => 'Muntilan',
            ],
            [
                'name' => 'Br. Agustinus Suparno, FIC',
                'role' => 'admin',
                'username' => 'aguspfic',
                'email' => 'aguspfic@fic.local',
                'community_location' => 'Yogyakarta',
            ],
            [
                'name' => 'Br. Andrias Purwanto, FIC',
                'role' => 'admin',
                'username' => 'wantofic',
                'email' => 'wantofic@fic.local',
                'community_location' => 'Semarang',
            ],
            // User / Bruder Muda (6 - 25)
            [
                'name' => 'Br. Alaysius Riyanto, FIC',
                'role' => 'user',
                'username' => 'riyantfic',
                'email' => 'riyantfic@fic.local',
            ],
            [
                'name' => 'Br. Fransiskus Saptono, FIC',
                'role' => 'user',
                'username' => 'saptofic',
                'email' => 'saptofic@fic.local',
            ],
            [
                'name' => 'Br. Thomas Aquinas, FIC',
                'role' => 'user',
                'username' => 'thomfic',
                'email' => 'thomfic@fic.local',
            ],
            [
                'name' => 'Br. Arcancio Amaral, FIC',
                'role' => 'user',
                'username' => 'canciofic',
                'email' => 'canciofic@fic.local',
            ],
            [
                'name' => 'Br. Hieronymus Wisnumurti R, FIC',
                'role' => 'user',
                'username' => 'hierofic',
                'email' => 'hierofic@fic.local',
            ],
            [
                'name' => 'Br. Hendrikus Arie Handoko, FIC',
                'role' => 'user',
                'username' => 'hendrikfic',
                'email' => 'hendrikfic@fic.local',
            ],
            [
                'name' => 'Br. Thomas Nova Wibisono, FIC',
                'role' => 'user',
                'username' => 'thnovafic',
                'email' => 'thnovafic@fic.local',
            ],
            [
                'name' => 'Br. Paulinus, FIC',
                'role' => 'user',
                'username' => 'paulfic',
                'email' => 'paulfic@fic.local',
            ],
            [
                'name' => 'Br. G. Anggara Tadon, FIC',
                'role' => 'user',
                'username' => 'anggafic',
                'email' => 'anggafic@fic.local',
            ],
            [
                'name' => 'Br. Yakobus Aditya, FIC',
                'role' => 'user',
                'username' => 'yakobfic',
                'email' => 'yakobfic@fic.local',
            ],
            [
                'name' => 'Br. Dinis Lopes, FIC',
                'role' => 'user',
                'username' => 'dinisfic',
                'email' => 'dinisfic@fic.local',
            ],
            [
                'name' => 'Br. Yohanes Zendi, FIC',
                'role' => 'user',
                'username' => 'zendific',
                'email' => 'zendific@fic.local',
            ],
            [
                'name' => 'Br. Jaimito Tan Tuames, FIC',
                'role' => 'user',
                'username' => 'jaimific',
                'email' => 'jaimific@fic.local',
            ],
            [
                'name' => 'Br. Gembira Fransiskus S, FIC',
                'role' => 'user',
                'username' => 'fransgfic',
                'email' => 'fransgfic@fic.local',
            ],
            [
                'name' => 'Br. Gregorius Yoan Danutama, FIC',
                'role' => 'user',
                'username' => 'gregfic',
                'email' => 'gregfic@fic.local',
            ],
            [
                'name' => 'Br. Laurentius Gde Gunawan S, FIC',
                'role' => 'user',
                'username' => 'bratafic',
                'email' => 'bratafic@fic.local',
            ],
            [
                'name' => 'Br. Philipus Aji Sapto Wibowo, FIC',
                'role' => 'user',
                'username' => 'ajisafic',
                'email' => 'ajisafic@fic.local',
            ],
            [
                'name' => 'Br. Constansius Afrisal S, FIC',
                'role' => 'user',
                'username' => 'constanfic',
                'email' => 'constanfic@fic.local',
            ],
            [
                'name' => 'Br. Remigio Cuil, FIC',
                'role' => 'user',
                'username' => 'remific',
                'email' => 'remific@fic.local',
            ],
            [
                'name' => 'Br. Justinus Coa, FIC',
                'role' => 'user',
                'username' => 'justfic',
                'email' => 'justfic@fic.local',
            ],
        ];

        foreach ($officialAccounts as $acc) {
            $existing = User::where('username', $acc['username'])->first();
            if ($existing) {
                $existing->name = $acc['name'];
                $existing->role = $acc['role'];
                if (isset($acc['community_location'])) {
                    $existing->community_location = $acc['community_location'];
                }
                $existing->is_active = true;
                $existing->save();
            } else {
                $created = new User();
                $created->name = $acc['name'];
                $created->username = $acc['username'];
                $created->role = $acc['role'];
                $created->email = $acc['email'];
                $created->password = $defaultPassword; // Password awal 123456
                $created->community_location = $acc['community_location'] ?? 'Indonesia';
                $created->is_active = true;
                $created->save();
            }
        }
    }
}
