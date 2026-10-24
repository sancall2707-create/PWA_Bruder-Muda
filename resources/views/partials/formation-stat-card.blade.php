{{-- Dua cincin formasi di tengah, catatan singkat di bawah, lalu baris akumulasi tahun. --}}
@php
    $ringOffsetTahun = 201 - (201 * (($stats['persentase_tahun'] ?? 0) / 100));
    $ringOffsetBulan = 201 - (201 * (($stats['persentase_bulan'] ?? 0) / 100));
    $statNote = $statNote ?? 'Dua kali sebulan untuk tiap bruder aktif: bimbingan rohani dan wawanhati.';
@endphp

<div class="progress-stat-card">
    <div class="stat-header">
        <span class="stat-title">Statistik Pembinaan</span>
        <span class="badge badge-scheduled">{{ $stats['label_bulan'] }} {{ now('Asia/Jakarta')->year }}</span>
    </div>

    <div class="stat-rings">
        <div class="stat-ring-item">
            <div class="stat-ring">
                <svg class="stat-ring-svg" viewBox="0 0 80 80">
                    <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" style="stroke:#E0E7FE;" />
                    <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="stroke:#1455C0; stroke-dasharray:201; stroke-dashoffset:{{ $ringOffsetTahun }};" />
                </svg>
                <div class="stat-ring-text" style="color:#1455C0;">{{ $stats['persentase_tahun'] }}%</div>
            </div>
            <span class="stat-ring-label">Akumulasi Tahunan</span>
            <span class="stat-ring-count year">{{ $stats['pembinaan_tahun'] }}/{{ $stats['target_tahun'] }}</span>
        </div>

        <div class="stat-ring-item">
            <div class="stat-ring">
                <svg class="stat-ring-svg" viewBox="0 0 80 80">
                    <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" style="stroke:#E0F2FE;" />
                    <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="stroke:#00838F; stroke-dasharray:201; stroke-dashoffset:{{ $ringOffsetBulan }};" />
                </svg>
                <div class="stat-ring-text" style="color:#00838F;">{{ $stats['persentase_bulan'] }}%</div>
            </div>
            <span class="stat-ring-label">Bulan Berjalan</span>
            <span class="stat-ring-count month">{{ $stats['pembinaan_bulan'] }}/{{ $stats['target_bulan'] }}</span>
        </div>
    </div>

    <p class="stat-note">{{ $statNote }}</p>

    <div class="stat-year-summary">
        <span>Akumulasi {{ $stats['label_tahunan'] }}</span>
        <strong>{{ $stats['pembinaan_tahun'] }} / {{ $stats['target_tahun'] }}</strong>
    </div>
</div>
