@extends('layouts.super-admin')

@section('content')
<!-- Monthly Progress Card -->
<div class="card" style="padding: 16px 18px; background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); border-color: #B9E6FE;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: #667085; text-transform: uppercase; letter-spacing: 0.5px;">
                Pembinaan Bulan {{ $stats['period_label'] ?? now()->translatedFormat('F Y') }}
            </div>
            <div style="font-size: 14px; font-weight: 800; color: #101828; margin-top: 2px;">
                Persentase Kepatuhan
            </div>
        </div>
        <div class="stat-val" style="color: #1455C0; font-size: 32px;">{{ $stats['completion_percent'] }}%</div>
    </div>
    <div style="height: 8px; background: #E0F2FE; border-radius: 999px; overflow: hidden;">
        <div style="width: {{ $stats['completion_percent'] }}%; height: 100%; background: linear-gradient(90deg, #1455C0, #003E97); border-radius: 999px; transition: width 0.6s ease;"></div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: #667085;">
        <span>{{ $stats['completed_reports'] }} dari {{ $stats['expected_reports'] }} laporan selesai</span>
        <span>{{ $stats['active_bruders'] }} bruder aktif</span>
    </div>
</div>

<!-- 4 Quick Stats Grid -->
<div class="stat-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
    <div class="stat-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div class="stat-val" style="font-size: 24px; font-weight: 800; color: #1455C0; line-height: 1.1;">{{ $stats['active_bruders'] }}</div>
        <div class="stat-label" style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Bruder Muda Aktif</div>
        <div style="font-size: 11px; color: #98A2B3; margin-top: 2px;">{{ $stats['inactive_bruders'] }} nonaktif</div>
    </div>

    <div class="stat-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div class="stat-val" style="font-size: 24px; font-weight: 800; color: #1455C0; line-height: 1.1;">{{ $stats['admins'] }}</div>
        <div class="stat-label" style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Admin / TPPBM</div>
    </div>

    <div class="stat-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div class="stat-val" style="font-size: 24px; font-weight: 800; color: #027A48; line-height: 1.1;">{{ $stats['bimbingan_submitted'] }}</div>
        <div class="stat-label" style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Laporan Bimbingan</div>
    </div>

    <div class="stat-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px 16px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
        <div class="stat-val" style="font-size: 24px; font-weight: 800; color: #027A48; line-height: 1.1;">{{ $stats['wawanhati_submitted'] }}</div>
        <div class="stat-label" style="font-size: 12px; font-weight: 600; color: #667085; margin-top: 4px;">Laporan Wawanhati</div>
    </div>
</div>

<!-- Quick Action Buttons -->
<div style="display: flex; flex-direction: column; gap: 10px;">
    <a href="{{ route('superadmin.users.index') }}" class="btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 20px; background: #1455C0; color: #fff; text-decoration: none; border-radius: 14px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(20,85,192,0.25);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        Kelola Data Bruder
    </a>

    <a href="{{ route('superadmin.monitoring.index') }}" class="btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 20px; background: #027A48; color: #fff; text-decoration: none; border-radius: 14px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(2,122,72,0.25);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        📑 Monitoring & Ekspor Data
    </a>

    <a href="{{ route('superadmin.sapaan.index') }}" class="btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 20px; background: #B57900; color: #fff; text-decoration: none; border-radius: 14px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(181,121,0,0.25);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        📢 Buat Sapaan Provinsial
    </a>
</div>

<!-- Recent Bruder Cards -->
<div class="section-title" style="font-size: 15px; font-weight: 800; color: #101828; margin-top: 8px; display: flex; align-items: center; justify-content: space-between;">
    <span>Daftar Bruder Muda</span>
    <a href="{{ route('superadmin.users.index') }}" class="btn-link" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Lihat Semua →</a>
</div>

<div style="display: flex; flex-direction: column; gap: 10px;">
    @forelse($bruders as $b)
        @php
            $monthDone = ($b->bimbingan_month_count ?? 0) + ($b->wawanhati_month_count ?? 0) + ($b->journal_month_count ?? 0);
        @endphp
        <div class="user-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
            <div>
                <div class="user-info-name" style="font-size: 14px; font-weight: 700; color: #101828;">{{ $b->name }}</div>
                <div class="user-info-loc" style="font-size: 12px; color: #667085; margin-top: 2px;">{{ $b->community_location ?? 'Komunitas belum diset' }}</div>
                <div style="display: flex; gap: 6px; margin-top: 8px;">
                    <span style="font-size: 11px; font-weight: 800; color: {{ $b->is_active ? '#027A48' : '#B42318' }}; background: {{ $b->is_active ? '#ECFDF3' : '#FEF3F2' }}; border-radius: 999px; padding: 3px 8px;">{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <span style="font-size: 11px; font-weight: 800; color: #1455C0; background: #EFF4FF; border-radius: 999px; padding: 3px 8px;">{{ $monthDone }}/3 bulan ini</span>
                </div>
            </div>
            <a href="{{ route('superadmin.users.track-record', $b->id) }}" class="btn" style="background: #1455C0; color: #fff; padding: 8px 14px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 12px; white-space: nowrap;">Track Record</a>
        </div>
    @empty
        <div class="card" style="text-align: center; color: #667085; padding: 20px;">Belum ada data bruder muda.</div>
    @endforelse
</div>
@endsection