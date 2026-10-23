<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed 11 akun resmi: Super Admin, Admin Ketua TPPBM, Admin TPPBM, dan Bruder Muda.
     */
    public function run(): void
    {
        $superAdminPassword = env('DEV_SUPER_ADMIN_PASSWORD', 'SuperAdmin#FIC2026');
        $adminPassword = env('DEV_ADMIN_PASSWORD', 'Admin#FIC2026');
        $ketuaPassword = env('DEV_KETUA_PASSWORD', 'Ketua#FIC2026');
        $userPassword = env('DEV_USER_PASSWORD', 'User#FIC2026');

        // 1. Super Admin (Administrator Data Raport)
        $superAdmin = User::firstOrNew(['username' => 'superadmin']);
        $superAdmin->name = 'Bruder Andrias Purwanto, FIC-Cah Ndeso';
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

        // 2. Admin Ketua TPPBM (Penanda Tangan Raport)
        $sarwo = User::firstOrNew(['username' => 'sarwoyes']);
        $sarwo->name = 'Br. Sarwo Ono, FIC';
        $sarwo->email = 'sarwoyes@fic.local';
        $sarwo->password = Hash::make($ketuaPassword);
        $sarwo->role = 'admin_ketua_tppbm';
        $sarwo->is_active = true;
        $sarwo->community_location = 'Semarang';
        $sarwo->phone = '081234567899';
        $sarwo->bio = 'Ketua Tim Pemantap Panggilan Bruder Muda (TPPBM)';
        $sarwo->save();

        // 3. Admin Ketua TPPBM (Wakil/Ketua Pendamping)
        $galih = User::firstOrNew(['username' => 'galihsih']);
        $galih->name = 'Br. F.A. Galih Sih Hartanta, FIC';
        $galih->email = 'fagsh08@gmail.com';
        $galih->password = Hash::make($ketuaPassword);
        $galih->role = 'admin_ketua_tppbm';
        $galih->is_active = true;
        $galih->community_location = 'Muntilan';
        $galih->phone = '087832139199';
        $galih->bio = 'Ketua Tim Pemantap Panggilan Bruder Muda (TPPBM)';
        $galih->birth_date = '1985-03-09';
        $galih->last_education = 'S2 Arsitektur';
        $galih->entry_year = 2003;
        $galih->save();

        // 4. Staf Admin TPPBM
        $admin = User::firstOrNew(['username' => 'admin']);
        $admin->name = 'Admin TPPBM';
        $admin->email = 'admin@fic.local';
        $admin->password = Hash::make($adminPassword);
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->community_location = 'Komunitas St. Joseph Semarang';
        $admin->phone = '081234567891';
        $admin->bio = 'Staf Tim Pendamping Pembinaan Bruder Muda (TPPBM)';
        $admin->save();

        // 5. Bruder Muda: userdemo
        $userDemo = User::firstOrNew(['username' => 'userdemo']);
        $userDemo->name = 'Br. Yohanes FIC';
        $userDemo->email = 'userdemo@fic.local';
        $userDemo->password = Hash::make($userPassword);
        $userDemo->role = 'user';
        $userDemo->is_active = true;
        $userDemo->community_location = 'Komunitas Bruderan Deltamas';
        $userDemo->phone = '081234567892';
        $userDemo->bio = 'Bruder Muda - Penugasan Unit Pendidikan Deltamas';
        $userDemo->birth_date = '1998-05-20';
        $userDemo->parents_name = 'Bpk. Yohanes & Ibu Maria';
        $userDemo->last_education = 'S1 Filsafat Keagamaan';
        $userDemo->entry_year = 2020;
        $userDemo->save();

        // 6. Bruder Muda: bruder_anton
        $anton = User::firstOrNew(['username' => 'bruder_anton']);
        $anton->name = 'Br. Antonius FIC';
        $anton->email = 'anton@fic.local';
        $anton->password = Hash::make($userPassword);
        $anton->role = 'user';
        $anton->is_active = true;
        $anton->community_location = 'Komunitas Bruderan Yogyakarta';
        $anton->phone = '081234567893';
        $anton->bio = 'Bruder Muda - Penugasan Asrama Yogyakarta';
        $anton->save();

        // 7. Bruder Muda: bruder_michael
        $michael = User::firstOrNew(['username' => 'bruder_michael']);
        $michael->name = 'Br. Michael FIC';
        $michael->email = 'michael@fic.local';
        $michael->password = Hash::make($userPassword);
        $michael->role = 'user';
        $michael->is_active = true;
        $michael->community_location = 'Komunitas Semarang';
        $michael->phone = '081299998888';
        $michael->bio = 'Bruder Muda';
        $michael->birth_date = '1999-08-12';
        $michael->save();

        // 8. Bruder Muda: brunofic
        $bruno = User::firstOrNew(['username' => 'brunofic']);
        $bruno->name = 'Br. Yohanes Sarwono, FIC';
        $bruno->email = 'brunofic@fic.local';
        $bruno->password = Hash::make($userPassword);
        $bruno->role = 'user';
        $bruno->is_active = true;
        $bruno->community_location = 'Jakarta';
        $bruno->phone = '021223254';
        $bruno->bio = 'Bruder Muda';
        $bruno->birth_date = '1945-12-12';
        $bruno->parents_name = 'Mento & Suratmi';
        $bruno->last_education = 'S2 Mbio';
        $bruno->entry_year = 2009;
        $bruno->save();

        // 9. Bruder Muda: poerw
        $poerw = User::firstOrNew(['username' => 'poerw']);
        $poerw->name = 'Br. Purwanto, FIC';
        $poerw->email = 'poerw@fic.local';
        $poerw->password = Hash::make($userPassword);
        $poerw->role = 'user';
        $poerw->is_active = true;
        $poerw->community_location = 'Semarang';
        $poerw->bio = 'Bruder Muda';
        $poerw->save();

        // 10. Bruder Muda: agsuparno
        $agsuparno = User::firstOrNew(['username' => 'agsuparno']);
        $agsuparno->name = 'Br. Agustinus Suparno, FIC';
        $agsuparno->email = 'agsuparno@fic.local';
        $agsuparno->password = Hash::make($userPassword);
        $agsuparno->role = 'user';
        $agsuparno->is_active = true;
        $agsuparno->community_location = 'Yogyakarta';
        $agsuparno->bio = 'Bruder Muda';
        $agsuparno->save();

        // 11. Bruder Muda: ysapto_ficindo
        $ysapto = User::firstOrNew(['username' => 'ysapto_ficindo']);
        $ysapto->name = 'Br. Yohanes Sapto Nugroho, FIC';
        $ysapto->email = 'ysapto@fic.local';
        $ysapto->password = Hash::make($userPassword);
        $ysapto->role = 'user';
        $ysapto->is_active = true;
        $ysapto->community_location = 'Surakarta';
        $ysapto->bio = 'Bruder Muda';
        $ysapto->save();
    }
}
