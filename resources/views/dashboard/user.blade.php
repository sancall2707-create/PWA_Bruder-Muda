@extends('layouts.user-app')

@section('content')
<!-- SECTION 1: STATISTIK PEMBINAAN BULAN INI -->
<!-- TODO: Replace placeholder calculation with dynamic sum from SpiritualGuidanceReport, WawanhatiReport, and SelfDevelopmentJournal -->
<div class="progress-stat-card">
    <div class="stat-header">
        <span class="stat-title">Statistik Pembinaan Bulan Ini</span>
        <span class="badge badge-scheduled">Oktober 2026</span>
    </div>
    <div class="stat-body">
        <div class="stat-ring">
            <svg class="stat-ring-svg" viewBox="0 0 80 80">
                <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" />
                <circle class="stat-ring-circle" cx="40" cy="40" r="32" />
            </svg>
            <div class="stat-ring-text">70%</div>
        </div>
        <div class="stat-info">
            <span class="stat-info-label">Pencapaian Target</span>
            <span class="stat-info-val">7 / 10</span>
            <span class="stat-info-sub">Laporan Selesai Dilaksanakan</span>
        </div>
    </div>
</div>

<!-- SECTION 2: REMINDER JADWAL TERDEKAT -->
<!-- TODO: Replace with query from upcoming schedules in next 7 days -->
<div class="reminder-card">
    <div class="reminder-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span>Pengingat Jadwal Terdekat</span>
    </div>
    <div class="reminder-list">
        <div class="reminder-item">
            <div class="reminder-bullet" style="background-color: var(--fic-primary);"></div>
            <div class="reminder-content">
                <div class="reminder-title">Bimbingan Rohani Bulanan</div>
                <div class="reminder-date">12 Oktober 2026 &bull; 10:00 WIB (Ruang Bimbingan)</div>
            </div>
        </div>
        <div class="reminder-item">
            <div class="reminder-bullet" style="background-color: var(--fic-cyan);"></div>
            <div class="reminder-content">
                <div class="reminder-title">Wawanhati Pimpinan Lokal (Piko)</div>
                <div class="reminder-date">14 Oktober 2026 &bull; 14:00 WIB (Ruang Piko Solo)</div>
            </div>
        </div>
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
