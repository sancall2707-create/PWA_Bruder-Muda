<?php

namespace Database\Seeders;

use App\Models\ProvincialMessage;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\User;
use App\Models\WawanhatiReport;
use Illuminate\Database\Seeder;

class PembinaanDataSeeder extends Seeder
{
    /**
     * Seed initial sample pembinaan records and provincial messages.
     */
    public function run(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $admin = User::where('username', 'admin')->first();
        $user = User::where('username', 'userdemo')->first();

        if ($user) {
            // 1. Laporan Bimbingan Rohani
            SpiritualGuidanceReport::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'mentor_name' => 'Br. Yohanes Subagio, FIC',
                    'meeting_at' => '2026-10-06 00:00:00',
                ],
                [
                    'location' => 'Ruang Bimbingan Ambarawa',
                    'report_content' => 'Dalam bimbingan rohani bulan ini, saya merenungkan kembali perjalanan panggilan sebagai Bruder FIC. Saya diajak oleh pembimbing untuk lebih tekun dalam doa pribadi dan menjaga semangat pelayanan di sekolah. Semua arahan sangat meneguhkan hati saya.',
                    'status' => 'submitted',
                    'submitted_at' => '2026-10-07 16:47:15',
                ]
            );

            SpiritualGuidanceReport::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'mentor_name' => 'Br. Yohanes Subagio, FIC',
                    'meeting_at' => '2026-10-20 00:00:00',
                ],
                [
                    'location' => 'Komunitas Deltamas',
                    'report_content' => 'Bimbingan kali ini saya diajak untk  merefleksikan diri mengenai pentingnya rendah hati. Mengarahkan diri semata hanya demi kemuliaan Tuhan. memang tidak mudah untuk dilaksanakan, namun tetap harus dijalankan sebagaimana mestinya.',
                    'status' => 'submitted',
                    'submitted_at' => '2026-10-20 01:16:04',
                ]
            );

            // 2. Laporan Wawanhati Pilok
            WawanhatiReport::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'local_leader_name' => 'Br. Fransiskus Xaverius, FIC',
                    'meeting_at' => '2026-10-20 00:00:00',
                ],
                [
                    'location' => 'Solo',
                    'report_content' => 'sementara ini memang tidak ada nabi tidak ada juga penguasa, jadi semua adalah saudara. saudara dalam panggilan pun saudara dalam menapaki perjalanan hidup yang kadang tidak mudah. Smeoga Allah yang maha baik senantiasa menyertaik kita',
                    'status' => 'submitted',
                    'submitted_at' => '2026-10-20 01:17:48',
                ]
            );

            // 3. Jurnal Pengembangan Diri
            SelfDevelopmentJournal::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'activity_name' => 'Seminar Pendampingan Orang Muda',
                    'activity_date' => '2026-10-20',
                ],
                [
                    'organizer' => 'Komunitas katolik Indonesia',
                    'speaker' => 'Prof Dr. Andrias Purwanto',
                    'description' => 'Kegiatan ini mengembangkan diri sebagai pribadi yang berakar pada kebutuhan orang mda dalam pengenala ndiri',
                    'reflection' => 'Sementara saya belum merefleksikan dari pengalaman ini. namun demikian bagi saya yang penting apdalah persoalan belajar. Belajar untuk menjadi diri sendiri dengan segala kesempatan berkembang meskipunn perlu perjuangan',
                    'status' => 'published',
                    'published_at' => '2026-10-20 01:20:54',
                ]
            );
        }

        // 4. Sapaan & Pengumuman Provinsial / TPPBM
        ProvincialMessage::firstOrCreate(
            ['title' => 'Pertemuan Pleno'],
            [
                'content' => 'Mohhon para bruder mambawa Brevir atau jika tidak maka wajib menginstall E-Katolik untuk keperluan berdoa bersama',
                'author_name' => 'Br. Andrias Purwanto, FIC',
                'author_role' => 'TPPBM',
                'kind' => 'pengumuman',
                'starts_at' => '2026-11-06 00:00:00',
                'is_active' => true,
                'published_at' => '2026-10-20 02:19:56',
                'created_by' => $admin?->id,
            ]
        );

        ProvincialMessage::firstOrCreate(
            ['title' => 'Ajakan santapan sabda'],
            [
                'content' => 'Hallo gays, jangan lupa sarapan pagi ini dengan santapan sabda. Temukan makna dari ayat kitab suci yang bermakna. Maaf ada Typo',
                'author_name' => 'Br. Agustinus Giwal Santoso, FIC',
                'author_role' => 'Pemimpin Provinsial FIC Indonesia',
                'kind' => 'sapaan',
                'is_active' => false,
                'published_at' => '2026-10-20 02:14:03',
                'created_by' => $superadmin?->id,
            ]
        );
    }
}
