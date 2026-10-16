@extends('layouts.user-app')

@section('content')
@php
    $ringOffset = 201 - (201 * (($stats['persentase'] ?? 0) / 100));
    $reminderColor = [
        'bimbingan' => 'var(--fic-primary)',
        'wawanhati' => 'var(--fic-cyan)',
        'lainnya' => 'var(--fic-gold)',
    ];
@endphp

<div class="progress-stat-card">
    <div class="stat-header">
        <span class="stat-title">Statistik Pembinaan Bulan Ini</span>
        <span class="badge badge-scheduled">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
    </div>
    <div class="stat-body">
        <div class="stat-ring">
            <svg class="stat-ring-svg" viewBox="0 0 80 80">
                <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" />
                <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="stroke-dashoffset: {{ $ringOffset }}" />
            </svg>
            <div class="stat-ring-text">{{ $stats['persentase'] }}%</div>
        </div>
        <div class="stat-info">
            <span class="stat-info-label">Pencapaian Target</span>
            <span class="stat-info-val">{{ $stats['total'] }} / {{ $stats['target'] }}</span>
            <span class="stat-info-sub">Laporan tersimpan bulan ini</span>
        </div>
    </div>
</div>

@if($sapaan)
    <a href="{{ route('sapaan-provinsial') }}" class="card" style="display:block; text-decoration:none; color:inherit; padding:14px;">
        <div style="font-size:11px; font-weight:800; letter-spacing:0.4px; color:var(--fic-gold); text-transform:uppercase;">Sapaan Provinsial</div>
        @if($sapaan->image)
            <img src="{{ route('sapaan-provinsial.foto', $sapaan) }}" alt="Foto sapaan" style="width:100%; height:auto; max-height:640px; object-fit:contain; border-radius:12px; margin-top:10px; display:block;">
        @endif
        <div style="font-weight:800; color:#101828; margin-top:8px;">{{ $sapaan->title }}</div>
        <div style="font-size:12px; color:#667085; margin-top:2px;">{{ $sapaan->author_name }} &bull; {{ $sapaan->publishedLabel() }}</div>
    </a>
@endif

<div class="reminder-card">
    <div class="reminder-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span>Pengingat Jadwal Terdekat</span>
    </div>
    <div class="reminder-list">
        @foreach($announcements as $announcement)
            <div class="reminder-item">
                <div class="reminder-bullet" style="background-color: var(--fic-gold);"></div>
                <div class="reminder-content">
                    <div class="reminder-title">{{ $announcement->title }}</div>
                    <div class="reminder-date">Pengumuman @if($announcement->scheduleLabel()) &bull; {{ $announcement->scheduleLabel() }} @endif</div>
                </div>
            </div>
        @endforeach
        @forelse($upcomingSchedules as $schedule)
            <div class="reminder-item">
                <div class="reminder-bullet" style="background-color: {{ $reminderColor[$schedule->category] ?? 'var(--fic-primary)' }};"></div>
                <div class="reminder-content">
                    <div class="reminder-title">{{ $schedule->title }}</div>
                    <div class="reminder-date">{{ $schedule->person }} &bull; {{ $schedule->date_formatted }} &bull; {{ $schedule->time_formatted }}@if($schedule->location) ({{ $schedule->location }})@endif</div>
                </div>
            </div>
        @empty
            @if($announcements->isEmpty())
                <div class="reminder-item">
                    <div class="reminder-content">
                        <div class="reminder-date">Belum ada jadwal dalam 7 hari ke depan.</div>
                    </div>
                </div>
            @endif
        @endforelse
    </div>
</div>

<!-- SECTION 3: MENU UTAMA BRUDER MUDA (Grid 2x2) -->
<div class="menu-section">
    <h2 class="section-title">MENU UTAMA</h2>
    <div class="menu-grid">
        <!-- 1. Bimbingan Rohani (Blue) -->
        <a href="{{ route('bimbingan.index') }}" class="menu-card blue">
            <div class="menu-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"></path>
                    <path d="M12 7v5l3 3"></path>
                </svg>
            </div>
            <div class="menu-card-text">
                <h3 class="menu-card-title">Bimbingan Rohani</h3>
                <p class="menu-card-desc">Laporan &amp; Jadwal</p>
            </div>
        </a>

        <!-- 2. Wawanhati (Cyan) -->
        <a href="{{ route('wawanhati.index') }}" class="menu-card cyan">
            <div class="menu-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="menu-card-text">
                <h3 class="menu-card-title">Wawanhati</h3>
                <p class="menu-card-desc">Pimpinan Lokal</p>
            </div>
        </a>

        <!-- 3. Agenda Pribadi (Gold) -->
        <a href="{{ route('agenda.index') }}" class="menu-card gold">
            <div class="menu-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div class="menu-card-text">
                <h3 class="menu-card-title">Agenda Pribadi</h3>
                <p class="menu-card-desc">Jadwal &amp; Rencana</p>
            </div>
        </a>

        <!-- 4. Pengembangan Diri (Green) -->
        <a href="{{ route('jurnal.index') }}" class="menu-card green">
            <div class="menu-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>
            <div class="menu-card-text">
                <h3 class="menu-card-title">Pengembangan Diri</h3>
                <p class="menu-card-desc">Jurnal &amp; Refleksi</p>
            </div>
        </a>
    </div>
</div>
@endsection
