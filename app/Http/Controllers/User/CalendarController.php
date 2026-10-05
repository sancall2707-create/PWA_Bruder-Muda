<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CalendarController extends Controller
{
    /**
     * Tampilkan kalender kegiatan Bruder Muda.
     */
    public function index(): View
    {
        // TEMPORARY DISPLAY DATA
        // Data agenda tanggal terpilih (12 Oktober 2026)
        $selectedDateAgendas = [
            (object) [
                'type' => 'bimbingan',
                'title' => 'Bimbingan Rohani Bersama Pembimbing',
                'time' => '10:00 - 11:30 WIB',
                'location' => 'Ruang Bimbingan Ambarawa',
                'person' => 'Br. Yohanes Subagio, FIC',
                'color' => 'blue',
            ],
            (object) [
                'type' => 'agenda',
                'title' => 'Doa Batin Bersama Komunitas',
                'time' => '18:00 - 19:00 WIB',
                'location' => 'Kapel Biara Komunitas',
                'person' => 'Semua Bruder Komunitas',
                'color' => 'gold',
            ],
        ];

        return view('user.kalender.index', compact('selectedDateAgendas'));
    }
}
