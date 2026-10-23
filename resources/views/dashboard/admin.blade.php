@extends('layouts.super-admin')

@php
    $title = 'Beranda TPPBM';
    $headerTitle = 'Beranda TPPBM';
    $headerSubtitle = 'Tim Pendamping Pembinaan Bruder Muda';
@endphp

@section('content')
<!-- Sambutan Pendamping & Akses Cepat PWA -->
<div class="card" style="padding: 16px 18px; background: #ffffff; border-radius: 16px; border: 1px solid #E4E7EC; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #1455C0, #003E97); color: #fff; font-weight: 800; font-size: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                TP
            </div>
            <div>
                <div style="font-size: 14px; font-weight: 800; color: #101828;">{{ $user->name }}</div>
                <div style="font-size: 12px; color: #667085; margin-top: 2px;">{{ $user->community_location ?? 'Tim Pendamping Pusat FIC' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Kartu Kepatuhan Pembinaan Bulanan -->
<div class="card" style="padding: 16px 18px; background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); border-color: #B9E6FE; border-radius: 16px; border-style: solid; border-width: 1px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: #667085; text-transform: uppercase; letter-spacing: 0.5px;">
                Pembinaan Bulan {{ $stats['period_label'] ?? now()->translatedFormat('F Y') }}
            </div>
            <div style="font-size: 14px; font-weight: 800; color: #101828; margin-top: 2px;">
                Tingkat Kepatuhan Laporan
            </div>
        </div>
        <div style="color: #1455C0; font-size: 30px; font-weight: 800;">{{ $stats['completion_percent'] }}%</div>
    </div>
    <div style="height: 8px; background: #E0F2FE; border-radius: 999px; overflow: hidden;">
        <div style="width: {{ $stats['completion_percent'] }}%; height: 100%; background: linear-gradient(90deg, #1455C0, #003E97); border-radius: 999px; transition: width 0.6s ease;"></div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: #667085;">
        <span>{{ $stats['completed_reports'] }} dari {{ $stats['expected_reports'] }} laporan selesai</span>
        <span>{{ $stats['active_bruders'] }} bruder aktif</span>
    </div>
</div>

<!-- 4 Grid Metrik TPPBM -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div style="font-size: 24px; font-weight: 800; color: #1455C0; line-height: 1.1;">{{ $stats['active_bruders'] }}</div>
        <div style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Bruder Didampingi</div>
        <div style="font-size: 11px; color: #98A2B3; margin-top: 2px;">{{ $stats['inactive_bruders'] }} nonaktif</div>
    </div>

    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div style="font-size: 24px; font-weight: 800; color: #027A48; line-height: 1.1;">{{ $stats['bimbingan_submitted'] }}</div>
        <div style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Bimbingan Rohani</div>
        <div style="font-size: 11px; color: #98A2B3; margin-top: 2px;">Laporan masuk bulan ini</div>
    </div>

    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div style="font-size: 24px; font-weight: 800; color: #027A48; line-height: 1.1;">{{ $stats['wawanhati_submitted'] }}</div>
        <div style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Wawanhati Pilok</div>
        <div style="font-size: 11px; color: #98A2B3; margin-top: 2px;">Laporan masuk bulan ini</div>
    </div>

    <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div style="font-size: 24px; font-weight: 800; color: #B57900; line-height: 1.1;">{{ $stats['journal_published'] }}</div>
        <div style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Jurnal Refleksi</div>
        <div style="font-size: 11px; color: #98A2B3; margin-top: 2px;">Dipublikasikan</div>
    </div>
</div>

<!-- Pengingat Jadwal Pembinaan Terdekat -->
<div style="margin-top: 4px;">
    <div style="font-size: 15px; font-weight: 800; color: #101828; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
        <span>Jadwal Pembinaan &amp; Agenda Terdekat</span>
        <a href="{{ route('superadmin.monitoring.agenda') }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Lihat Kalender →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px;">
        @forelse($upcomingSchedules as $sched)
            <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 12px 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 4px rgba(16,24,40,0.02);">
                <div style="width: 4px; height: 36px; border-radius: 4px; background: {{ $sched->color }}; flex-shrink: 0;"></div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; font-weight: 700; color: #101828;">{{ $sched->bruder }}</span>
                        <span style="font-size: 11px; font-weight: 700; color: {{ $sched->color }};">{{ $sched->type }}</span>
                    </div>
                    <div style="font-size: 12px; color: #475467; margin-top: 2px;">{{ $sched->title }}</div>
                    <div style="font-size: 11px; color: #667085; margin-top: 2px;">{{ $sched->datetime }} &bull; {{ $sched->location }}</div>
                </div>
            </div>
        @empty
            <div style="background: #ffffff; border-radius: 14px; border: 1px dashed #D0D5DD; padding: 16px; text-align: center; color: #667085; font-size: 13px;">
                Belum ada jadwal bimbingan atau wawanhati terdekat.
            </div>
        @endforelse
    </div>
</div>

<!-- Feed Laporan Masuk Terbaru -->
<div style="margin-top: 6px;">
    <div style="font-size: 15px; font-weight: 800; color: #101828; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
        <span>Laporan &amp; Refleksi Terbaru</span>
        <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Semua Laporan →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 10px;">
        @forelse($recentFeed as $feed)
            <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                    <div>
                        <div style="font-weight: 700; font-size: 14px; color: #101828;">{{ $feed->user->name ?? 'Bruder Muda' }}</div>
                        <div style="font-size: 12px; color: #667085; margin-top: 2px;">{{ $feed->type_label }} &bull; {{ $feed->date_label }}</div>
                    </div>
                    <span style="padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; background: {{ $feed->bg_color }}; color: {{ $feed->theme_color }}; border: 1px solid {{ $feed->border_color }};">
                        {{ $feed->status_label }}
                    </span>
                </div>
                <div style="margin-top: 6px; font-size: 12px; font-weight: 600; color: #344054;">
                    {{ $feed->counterpart }}
                </div>
                @if(!empty($feed->preview))
                    <div style="margin-top: 8px; padding: 10px 12px; background: #F8FAF9; border-left: 3px solid {{ $feed->theme_color }}; border-radius: 0 8px 8px 0; font-size: 12px; color: #374151; line-height: 1.5;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($feed->preview), 160) }}
                    </div>
                @endif
                @if($feed->user)
                    <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                        <a href="{{ route('superadmin.users.track-record', $feed->user->id) }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">
                            Lihat Track Record →
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div style="background: #ffffff; border-radius: 14px; border: 1px dashed #D0D5DD; padding: 16px; text-align: center; color: #667085; font-size: 13px;">
                Belum ada laporan atau jurnal masuk bulan ini.
            </div>
        @endforelse
    </div>
</div>

<!-- Daftar Bruder Muda Didampingi -->
<div style="margin-top: 6px;">
    <div style="font-size: 15px; font-weight: 800; color: #101828; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
        <span>Daftar Bruder Muda</span>
        <a href="{{ route('superadmin.users.index') }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Semua Direktori →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 10px;">
        @forelse($bruders as $b)
            @php
                $monthDone = ($b->bimbingan_month_count ?? 0) + ($b->wawanhati_month_count ?? 0);
                $words = preg_split('/\s+/', trim($b->name)) ?: [];
                $initials = strtoupper(substr($words[0] ?? 'B', 0, 1).substr($words[1] ?? '', 0, 1));
            @endphp
            <div style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #1455C0, #003E97); color: #fff; font-weight: 800; font-size: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        {{ $initials }}
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #101828;">{{ $b->name }}</div>
                        <div style="font-size: 12px; color: #667085; margin-top: 2px;">{{ $b->community_location ?? 'Komunitas belum diset' }}</div>
                        <div style="display: flex; gap: 6px; margin-top: 6px;">
                            <span style="font-size: 11px; font-weight: 800; color: {{ $b->is_active ? '#027A48' : '#B42318' }}; background: {{ $b->is_active ? '#ECFDF3' : '#FEF3F2' }}; border-radius: 999px; padding: 2px 8px;">
                                {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <span style="font-size: 11px; font-weight: 800; color: #1455C0; background: #EFF4FF; border-radius: 999px; padding: 2px 8px;">
                                {{ $monthDone }}/2 bulan ini
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('superadmin.users.track-record', $b->id) }}" style="background: #1455C0; color: #fff; padding: 8px 14px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 12px; white-space: nowrap;">
                    Track Record
                </a>
            </div>
        @empty
            <div style="background: #ffffff; border-radius: 14px; border: 1px dashed #D0D5DD; padding: 16px; text-align: center; color: #667085; font-size: 13px;">
                Belum ada data bruder muda.
            </div>
        @endforelse
    </div>
</div>
@endsection
