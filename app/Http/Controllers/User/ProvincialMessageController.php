<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProvincialMessage;
use Illuminate\Contracts\View\View;

class ProvincialMessageController extends Controller
{
    /**
     * Tampilkan Sapaan Provinsial untuk Bruder Muda.
     */
    public function index(): View
    {
        $dbMessage = ProvincialMessage::where('is_active', true)
            ->latest('published_at')
            ->first();

        if ($dbMessage) {
            $message = (object) [
                'title' => $dbMessage->title,
                'author_name' => $dbMessage->author_name ?? 'Br. Agustinus Giwal Santoso, FIC',
                'author_role' => 'Pemimpin Provinsial FIC Indonesia',
                'published_at' => $dbMessage->published_at ? $dbMessage->published_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
                'image' => $dbMessage->image ? asset('storage/' . $dbMessage->image) : null,
                'content' => array_filter(explode("\n", $dbMessage->content)),
            ];
        } else {
            // Default Message jika belum ada publikasi dari admin
            $message = (object) [
                'title' => 'Menghidupi Panggilan dengan Sukacita dan Kesetiaan',
                'author_name' => 'Br. Agustinus Giwal Santoso, FIC',
                'author_role' => 'Pemimpin Provinsial FIC Indonesia',
                'published_at' => now()->translatedFormat('d F Y'),
                'image' => null,
                'content' => [
                    'Para Bruder Muda yang terkasih dalam Kristus dan Santo Pelindung kita, perziarahan hidup panggilan religius adalah anugerah suci yang dipercayakan Allah kepada kita setiap hari. Dalam setiap langkah pembinaan awal, kesetiaan pada hal-hal kecil menjadi batu penjuru bagi kedewasaan rohani dan persaudaraan sejati.',
                    'Melalui bimbingan rohani yang teratur, wawanhati yang jujur bersama pimpinan lokal, serta refleksi pribadi dalam jurnal harian, kita diajak untuk terus memurnikan motivasi penyerahan diri kita bagi Allah dan sesama, seturut teladan pendiri Kongregasi kita.',
                    'Mari kita jalani masa pembinaan ini dengan hati terbuka, rendah hati, dan penuh sukacita. Semoga Tuhan senantiasa meneguhkan langkah kita semua.',
                ],
            ];
        }

        return view('user.sapaan-provinsial', compact('message'));
    }
}
