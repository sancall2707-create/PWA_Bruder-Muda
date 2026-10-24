@extends('layouts.super-admin')

@section('content')
@include('partials.formation-stat-card')

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

<!-- Jadwal Pembinaan & Seluruh Agenda Terdekat -->
<div style="margin-top: 10px;">
    <div style="font-size: 15px; font-weight: 800; color: #101828; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
        <span>Jadwal Pembinaan &amp; Agenda Terdekat</span>
        <a href="{{ route('superadmin.monitoring.agenda') }}" style="font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Lihat Kalender →</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px;">
        @forelse($upcomingSchedules ?? [] as $sched)
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
                Belum ada jadwal kegiatan atau agenda bruder terdekat.
            </div>
        @endforelse
    </div>
</div>

<!-- Recent Bruder Cards -->
<div class="section-title" style="font-size: 15px; font-weight: 800; color: #101828; margin-top: 8px; display: flex; align-items: center; justify-content: space-between;">
    <span>Daftar Bruder Muda</span>
    <a href="{{ route('superadmin.users.index') }}" class="btn-link" style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #1455C0; text-decoration: none;">Lihat Semua →</a>
</div>

<div style="display: flex; flex-direction: column; gap: 10px;">
    @forelse($bruders as $b)
        @php
            $monthDone = ($b->bimbingan_month_count ?? 0) + ($b->wawanhati_month_count ?? 0);
        @endphp
        <div class="user-card" style="background: #ffffff; border-radius: 14px; border: 1px solid #E4E7EC; padding: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; box-shadow: 0 2px 6px rgba(16,24,40,0.03);">
            <div>
                <div class="user-info-name" style="font-size: 14px; font-weight: 700; color: #101828;">{{ $b->name }}</div>
                <div class="user-info-loc" style="font-size: 12px; color: #667085; margin-top: 2px;">{{ $b->community_location ?? 'Komunitas belum diset' }}</div>
                <div style="display: flex; gap: 6px; margin-top: 8px;">
                    <span style="font-size: 11px; font-weight: 800; color: {{ $b->is_active ? '#027A48' : '#B42318' }}; background: {{ $b->is_active ? '#ECFDF3' : '#FEF3F2' }}; border-radius: 999px; padding: 3px 8px;">{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <span style="font-size: 11px; font-weight: 800; color: #1455C0; background: #EFF4FF; border-radius: 999px; padding: 3px 8px;">{{ $monthDone }}/2 bulan ini</span>
                </div>
            </div>
            <a href="{{ route('superadmin.users.track-record', $b->id) }}" class="btn" style="background: #1455C0; color: #fff; padding: 8px 14px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 12px; white-space: nowrap;">Track Record</a>
        </div>
    @empty
        <div class="card" style="text-align: center; color: #667085; padding: 20px;">Belum ada data bruder muda.</div>
    @endforelse
</div>
@endsection