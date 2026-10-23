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

@if(!empty($unreadMessagesCount) && $unreadMessagesCount > 0)
    <a href="{{ route('pesan.index') }}" class="card" style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; background:linear-gradient(135deg, #FFF9EB 0%, #FEF0C7 100%); border:1px solid #FECDCA; text-decoration:none; color:inherit; margin-bottom:14px; border-radius:16px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:38px; height:38px; border-radius:10px; background:#B57900; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <div>
                <div style="font-weight:800; font-size:13px; color:#7A4D00;">Pesan Khusus Baru ({{ $unreadMessagesCount }})</div>
                <div style="font-size:12px; color:#936000; margin-top:2px;">{{ $latestUnreadMessage->title ?? 'Ada pesan dari Pimpinan/TPPBM' }}</div>
            </div>
        </div>
        <span style="font-size:11px; font-weight:800; color:#B57900; background:#fff; padding:6px 12px; border-radius:999px; box-shadow:0 1px 3px rgba(0,0,0,0.06); white-space:nowrap;">
            Buka &rarr;
        </span>
    </a>
@endif

<!-- STATISTIK PEMBINAAN: KIRI (AKUMULASI TAHUNAN JAN–SEKARANG) vs KANAN (BULAN BERJALAN) -->
<div class="progress-stat-card">
    <div class="stat-header">
        <span class="stat-title">Statistik Pembinaan Formatif</span>
        <span class="badge badge-scheduled">{{ $stats['label_bulan'] }} {{ now('Asia/Jakarta')->year }}</span>
    </div>
    <div class="stat-body">
        <!-- SEBELAH KIRI: Akumulasi Tahunan (Januari s.d Tanggal Saat Ini, Target 24) -->
        <div class="stat-ring">
            <svg class="stat-ring-svg" viewBox="0 0 80 80">
                <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" />
                <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="stroke-dashoffset: {{ $ringOffset }}" />
            </svg>
            <div class="stat-ring-text">{{ $stats['persentase_tahun'] }}%</div>
        </div>

        <!-- SEBELAH KANAN: Realisasi Bulan Berjalan (Reset tiap bulan baru ke 0/2) -->
        <div class="stat-info">
            <span class="stat-info-label">{{ $stats['label_bulan'] }} ({{ $stats['persentase_bulan'] }}%)</span>
            <span class="stat-info-val">{{ $stats['pembinaan_bulan'] }} / 2</span>
            <span class="stat-info-sub">Target 2 kali per bulan (Bimbingan &amp; Wawanhati)</span>
            <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(20,85,192,0.18); font-size: 11px; font-weight: 700; color: #1455C0;">
                Akumulasi {{ $stats['label_tahunan'] }}: <strong>{{ $stats['pembinaan_tahun'] }} / 24</strong>
            </div>
        </div>
    </div>
</div>

@if($stats['pembinaan_bulan'] >= 2)
    <div style="background: linear-gradient(135deg, #ECFDF3 0%, #D1FADF 100%); border: 1px solid #A6F4C5; border-radius: 16px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #12B76A; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px;">
            &#10004;
        </div>
        <div>
            <div style="font-size: 13px; font-weight: 800; color: #027A48;">Profisiat! Target Pembinaan Lengkap</div>
            <div style="font-size: 11px; color: #05603A; margin-top: 2px;">Target pembinaan bulan {{ now()->locale('id')->translatedFormat('F') }} telah terpenuhi (2/2). Tetap setia dalam peziarahan panggilan.</div>
        </div>
    </div>
@endif

<!-- CHECKLIST INSTRUMEN BULAN INI (SIMETRIS & RAPI) -->
<div class="card" style="padding: 16px; margin-bottom: 14px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div style="font-size:12px; font-weight:800; color:#101828; text-transform:uppercase; letter-spacing:0.5px;">
            Checklist Formasi {{ now()->locale('id')->translatedFormat('F') }}
        </div>
        <span style="font-size:10px; font-weight:700; color:#475467; background:#F2F4F7; padding:3px 8px; border-radius:999px; white-space:nowrap;">
            3 Instrumen
        </span>
    </div>

    <div style="display:flex; flex-direction:column; gap:8px;">
        <!-- 1. Bimbingan Rohani -->
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-radius:12px; min-height:54px; box-sizing:border-box; background:{{ $stats['checklist']['bimbingan']['done'] ? '#F6FEF9; border:1px solid #D1FADF;' : '#F8F9FC; border:1px solid #EAECF0;' }}">
            <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1;">
                <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; {{ $stats['checklist']['bimbingan']['done'] ? 'background:#12B76A; color:#fff;' : 'background:#E4E7EC; color:#667085;' }}">
                    {{ $stats['checklist']['bimbingan']['done'] ? '✓' : '1' }}
                </div>
                <div style="min-width:0; flex:1;">
                    <div style="font-size:13px; font-weight:700; color:#101828; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Bimbingan Rohani</div>
                    <div style="font-size:11px; color:#667085; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px;">
                        @if($stats['checklist']['bimbingan']['done'])
                            Terlaksana: {{ $stats['checklist']['bimbingan']['item']->meeting_at->locale('id')->translatedFormat('d M Y') }}
                        @else
                            Wajib 1x sebulan
                        @endif
                    </div>
                </div>
            </div>
            @if($stats['checklist']['bimbingan']['done'])
                <a href="{{ route('bimbingan.index') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#027A48; text-decoration:none; background:#ECFDF3; border:1px solid #A6F4C5; border-radius:999px; box-sizing:border-box; flex-shrink:0;">Lihat &rarr;</a>
            @else
                <a href="{{ route('bimbingan.create') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#fff; text-decoration:none; background:#1455C0; border-radius:999px; box-sizing:border-box; flex-shrink:0;">+ Isi</a>
            @endif
        </div>

        <!-- 2. Wawanhati Pimpinan Lokal -->
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-radius:12px; min-height:54px; box-sizing:border-box; background:{{ $stats['checklist']['wawanhati']['done'] ? '#F6FEF9; border:1px solid #D1FADF;' : '#F8F9FC; border:1px solid #EAECF0;' }}">
            <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1;">
                <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; {{ $stats['checklist']['wawanhati']['done'] ? 'background:#12B76A; color:#fff;' : 'background:#E4E7EC; color:#667085;' }}">
                    {{ $stats['checklist']['wawanhati']['done'] ? '✓' : '2' }}
                </div>
                <div style="min-width:0; flex:1;">
                    <div style="font-size:13px; font-weight:700; color:#101828; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Wawanhati Pimpinan Lokal</div>
                    <div style="font-size:11px; color:#667085; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px;">
                        @if($stats['checklist']['wawanhati']['done'])
                            Terlaksana: {{ $stats['checklist']['wawanhati']['item']->meeting_at->locale('id')->translatedFormat('d M Y') }}
                        @else
                            Wajib 1x sebulan
                        @endif
                    </div>
                </div>
            </div>
            @if($stats['checklist']['wawanhati']['done'])
                <a href="{{ route('wawanhati.index') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#027A48; text-decoration:none; background:#ECFDF3; border:1px solid #A6F4C5; border-radius:999px; box-sizing:border-box; flex-shrink:0;">Lihat &rarr;</a>
            @else
                <a href="{{ route('wawanhati.create') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#fff; text-decoration:none; background:#00838F; border-radius:999px; box-sizing:border-box; flex-shrink:0;">+ Isi</a>
            @endif
        </div>

        <!-- 3. Jurnal Pengembangan Diri -->
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-radius:12px; min-height:54px; box-sizing:border-box; background:{{ $stats['checklist']['jurnal']['done'] ? '#F6FEF9; border:1px solid #D1FADF;' : '#F8F9FC; border:1px solid #EAECF0;' }}">
            <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1;">
                <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; {{ $stats['checklist']['jurnal']['done'] ? 'background:#12B76A; color:#fff;' : 'background:#E4E7EC; color:#667085;' }}">
                    {{ $stats['checklist']['jurnal']['done'] ? '✓' : '3' }}
                </div>
                <div style="min-width:0; flex:1;">
                    <div style="font-size:13px; font-weight:700; color:#101828; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Jurnal Pengembangan Diri</div>
                    <div style="font-size:11px; color:#667085; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px;">
                        @if($stats['checklist']['jurnal']['done'])
                            {{ $stats['checklist']['jurnal']['count'] }} jurnal tercatat bulan ini
                        @else
                            Refleksi seminar/retret/studi
                        @endif
                    </div>
                </div>
            </div>
            @if($stats['checklist']['jurnal']['done'])
                <a href="{{ route('jurnal.index') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#027A48; text-decoration:none; background:#ECFDF3; border:1px solid #A6F4C5; border-radius:999px; box-sizing:border-box; flex-shrink:0;">Lihat &rarr;</a>
            @else
                <a href="{{ route('jurnal.create') }}" style="width:76px; min-width:76px; height:32px; display:inline-flex; align-items:center; justify-content:center; text-align:center; font-size:11px; font-weight:800; color:#fff; text-decoration:none; background:#027A48; border-radius:999px; box-sizing:border-box; flex-shrink:0;">+ Isi</a>
            @endif
        </div>
    </div>
</div>

@if($sapaan)
    <a href="{{ route('sapaan-provinsial') }}" class="card" style="display:block; text-decoration:none; color:inherit; padding:14px;">
        <div style="font-size:11px; font-weight:800; letter-spacing:0.4px; color:var(--fic-gold); text-transform:uppercase;">{{ $sapaan->sapaanLabel() }}</div>
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

<!-- KARTU AJAKAN INSTALASI PWA (Tampil otomatis jika dibuka via browser biasa) -->
<div id="pwaInstallCard" class="card" style="display:none; padding:14px 16px; background:linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%); border:1px solid #B9E6FE; border-radius:16px; margin-top:14px;">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:10px; background:#1455C0; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </div>
            <div>
                <div style="font-size:13px; font-weight:800; color:#0E3366;">Pasang Aplikasi di HP</div>
                <div style="font-size:11px; color:#475467; margin-top:2px;">Akses cepat &amp; hemat kuota di layar utama</div>
            </div>
        </div>
        <button type="button" id="pwaInstallBtn" style="border:none; background:#1455C0; color:#fff; font-size:11px; font-weight:800; padding:8px 14px; border-radius:999px; cursor:pointer; white-space:nowrap;">
            Pasang
        </button>
    </div>
</div>

<script>
    (function() {
        var deferredPrompt;
        var card = document.getElementById('pwaInstallCard');
        var btn = document.getElementById('pwaInstallBtn');
        if (!window.matchMedia('(display-mode: standalone)').matches && card) {
            window.addEventListener('beforeinstallprompt', function(e) {
                e.preventDefault();
                deferredPrompt = e;
                card.style.display = 'block';
            });
            if (btn) {
                btn.addEventListener('click', function() {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        deferredPrompt.userChoice.then(function() {
                            card.style.display = 'none';
                            deferredPrompt = null;
                        });
                    }
                });
            }
        }
    })();
</script>
@endsection
