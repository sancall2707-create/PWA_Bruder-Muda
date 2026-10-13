<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PersonalAgenda;
use App\Models\SelfDevelopmentJournal;
use App\Models\SpiritualGuidanceReport;
use App\Models\User;
use App\Models\WawanhatiReport;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $bruderFilter = $request->input('bruder_filter');
        $monthFilter  = $request->input('month_filter');

        $bruders = User::where('role', 'user')->orderBy('name')->get();

        $bimbingans = SpiritualGuidanceReport::with('user')
            ->when($bruderFilter, fn($q) => $q->where('user_id', $bruderFilter))
            ->when($monthFilter, fn($q) => $q->whereMonth('meeting_at', $monthFilter))
            ->latest('meeting_at')->take(30)->get()
            ->map(fn($r) => (object)[
                'user'          => $r->user,
                'date'          => $r->meeting_at,
                'type_label'    => 'Bimbingan Rohani',
                'preview'       => $r->mentor_name,
                'status'        => $r->status,
                'status_text'   => 'Terkirim',
                'status_bg'     => '#EFF8FF',
                'status_color'  => '#1455C0',
                'status_border' => '#B2DDFF',
            ]);

        $wawanhatis = WawanhatiReport::with('user')
            ->when($bruderFilter, fn($q) => $q->where('user_id', $bruderFilter))
            ->when($monthFilter, fn($q) => $q->whereMonth('meeting_at', $monthFilter))
            ->latest('meeting_at')->take(30)->get()
            ->map(fn($r) => (object)[
                'user'          => $r->user,
                'date'          => $r->meeting_at,
                'type_label'    => 'Wawanhati Pilok',
                'preview'       => $r->local_leader_name,
                'status'        => $r->status,
                'status_text'   => 'Terkirim',
                'status_bg'     => '#F0FDF4',
                'status_color'  => '#027A48',
                'status_border' => '#ABEFC6',
            ]);

        $journals = SelfDevelopmentJournal::with('user')
            ->when($bruderFilter, fn($q) => $q->where('user_id', $bruderFilter))
            ->when($monthFilter, fn($q) => $q->whereMonth('activity_date', $monthFilter))
            ->latest('activity_date')->take(30)->get()
            ->map(fn($r) => (object)[
                'user'          => $r->user,
                'date'          => $r->activity_date,
                'type_label'    => 'Jurnal Pengembangan Diri',
                'preview'       => $r->activity_name,
                'status'        => $r->status,
                'status_text'   => 'Dipublikasi',
                'status_bg'     => '#FFF8E1',
                'status_color'  => '#B57900',
                'status_border' => '#FFD966',
            ]);

        $reports = $bimbingans->merge($wawanhatis)->merge($journals)
            ->sortByDesc(fn($r) => $r->date)
            ->values();

        return view('super-admin.monitoring.index', compact('reports', 'bruders'));
    }

    public function agenda(Request $request)
    {
        $bruderFilter = $request->input('bruder_filter');

        $bruders = User::where('role', 'user')->orderBy('name')->get();

        $agendas = PersonalAgenda::with('user')
            ->when($bruderFilter, fn($q) => $q->where('user_id', $bruderFilter))
            ->orderBy('start_at')
            ->take(40)->get();

        return view('super-admin.monitoring.agenda', compact('agendas', 'bruders'));
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'rekap-laporan-pwa-bruder-muda-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tipe Laporan', 'Nama Bruder', 'Tanggal', 'Pembimbing/Pimpinan', 'Status', 'Isi/Judul']);

            foreach (SpiritualGuidanceReport::with('user')->latest()->get() as $b) {
                fputcsv($handle, ['Bimbingan Rohani', $b->user->name ?? '-', optional($b->meeting_at)->format('Y-m-d') ?? '-', $b->mentor_name, $b->status, substr(strip_tags($b->report_content ?? ''), 0, 100)]);
            }

            foreach (WawanhatiReport::with('user')->latest()->get() as $w) {
                fputcsv($handle, ['Wawanhati Pilok', $w->user->name ?? '-', optional($w->meeting_at)->format('Y-m-d') ?? '-', $w->local_leader_name, $w->status, substr(strip_tags($w->report_content ?? ''), 0, 100)]);
            }

            foreach (SelfDevelopmentJournal::with('user')->latest()->get() as $j) {
                fputcsv($handle, ['Jurnal', $j->user->name ?? '-', optional($j->activity_date)->format('Y-m-d') ?? '-', $j->organizer, $j->status, substr(strip_tags($j->activity_name ?? ''), 0, 100)]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
