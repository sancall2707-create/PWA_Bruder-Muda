<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ProvincialMessageController extends Controller
{
    /**
     * Tampilkan Sapaan Provinsial untuk Bruder Muda.
     */
    public function index(): View
    {
        // TEMPORARY DISPLAY DATA - Sapaan Provinsial
        // TODO: Replace with database query: ProvincialMessage::where('is_active', true)->latest('published_at')->first()
        $message = (object) [
            'title' => 'Menghidupi Panggilan dengan Sukacita dan Kesetiaan',
            'author_name' => 'Br. Agustinus Sudaryanto, FIC',
            'author_role' => 'Pemimpin Provinsial FIC Indonesia',
            'published_at' => '01 Oktober 2026',
            'content' => [
                'Para Bruder Muda yang terkasih dalam Kristus dan Santo Pelindung kita, perziarahan hidup panggilan religius adalah anugerah suci yang dipercayakan Allah kepada kita setiap hari. Dalam setiap langkah pembinaan awal, kesetiaan pada hal-hal kecil menjadi batu penjuru bagi kedewasaan rohani dan persaudaraan sejati.',
                'Melalui bimbingan rohani yang teratur, wawanhati yang jujur bersama pimpinan lokal, serta refleksi pribadi dalam jurnal harian, kita diajak untuk terus memurnikan motivasi penyerahan diri kita bagi Allah dan sesama, seturut teladan pendiri Kongregasi kita.',
                'Mari kita jalani masa pembinaan ini dengan hati terbuka, rendah hati, dan penuh sukacita. Semoga Tuhan senantiasa meneguhkan langkah kita semua.',
            ],
        ];

        return view('user.sapaan-provinsial', compact('message'));
    }
}
