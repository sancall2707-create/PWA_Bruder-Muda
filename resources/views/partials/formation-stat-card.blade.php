{{-- Dua cincin formasi + baris akumulasi tahun selebar kartu, sama seperti beranda bruder. --}}
@php
    $ringOffsetTahun = 201 - (201 * (($stats['persentase_tahun'] ?? 0) / 100));
    $ringOffsetBulan = 201 - (201 * (($stats['persentase_bulan'] ?? 0) / 100));
@endphp

<div class="progress-stat-card">
    <div class="stat-header">
        <span class="stat-title">Statistik Pembinaan Formatif</span>
        <span class="badge badge-scheduled">{{ $stats['label_bulan'] }} {{ now('Asia/Jakarta')->year }}</span>
    </div>

    <div class="stat-body" style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:12px; flex-shrink:0;">
            <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                <div class="stat-ring" style="width:68px; height:68px; position:relative;">
                    <svg class="stat-ring-svg" viewBox="0 0 80 80" style="transform:rotate(-90deg); width:100%; height:100%;">
                        <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" style="fill:none; stroke:#E0E7FE; stroke-width:7;" />
                        <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="fill:none; stroke:#1455C0; stroke-width:7; stroke-linecap:round; stroke-dasharray:201; stroke-dashoffset:{{ $ringOffsetTahun }}; transition:stroke-dashoffset 0.8s ease;" />
                    </svg>
                    <div class="stat-ring-text" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:13px; font-weight:800; color:#1455C0;">
                        {{ $stats['persentase_tahun'] }}%
                    </div>
                </div>
                <span style="font-size:10px; font-weight:800; color:#475467; text-align:center; white-space:nowrap; letter-spacing:0.2px;">
                    Akumulasi Tahunan
                </span>
                <span style="font-size:9.5px; font-weight:700; color:#1455C0; background:#EFF4FF; padding:1px 6px; border-radius:999px;">
                    {{ $stats['pembinaan_tahun'] }}/{{ $stats['target_tahun'] }}
                </span>
            </div>

            <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                <div class="stat-ring" style="width:68px; height:68px; position:relative;">
                    <svg class="stat-ring-svg" viewBox="0 0 80 80" style="transform:rotate(-90deg); width:100%; height:100%;">
                        <circle class="stat-ring-circle-bg" cx="40" cy="40" r="32" style="fill:none; stroke:#E0F2FE; stroke-width:7;" />
                        <circle class="stat-ring-circle" cx="40" cy="40" r="32" style="fill:none; stroke:#00838F; stroke-width:7; stroke-linecap:round; stroke-dasharray:201; stroke-dashoffset:{{ $ringOffsetBulan }}; transition:stroke-dashoffset 0.8s ease;" />
                    </svg>
                    <div class="stat-ring-text" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:13px; font-weight:800; color:#00838F;">
                        {{ $stats['persentase_bulan'] }}%
                    </div>
                </div>
                <span style="font-size:10px; font-weight:800; color:#475467; text-align:center; white-space:nowrap; letter-spacing:0.2px;">
                    Bulan Berjalan
                </span>
                <span style="font-size:9.5px; font-weight:700; color:#00838F; background:#E0F7FA; padding:1px 6px; border-radius:999px;">
                    {{ $stats['pembinaan_bulan'] }}/{{ $stats['target_bulan'] }}
                </span>
            </div>
        </div>

        <div class="stat-info" style="flex:1; min-width:160px;">
            <div style="font-size:11px; font-weight:800; color:#667085; text-transform:uppercase; letter-spacing:0.4px;">
                Target Formasi Bulanan
            </div>
            <div style="font-size:22px; font-weight:800; color:#101828; line-height:1.2; margin-top:2px;">
                {{ $stats['pembinaan_bulan'] }} <span style="font-size:15px; font-weight:600; color:#667085;">/ {{ $stats['target_bulan'] }} Laporan</span>
            </div>
            <div style="font-size:11px; color:#475467; margin-top:3px; line-height:1.4;">
                Target 2 kali per bruder aktif (Bimbingan Rohani &amp; Wawanhati Pilok).
            </div>
        </div>
    </div>

    <div class="stat-year-summary">
        <span>Akumulasi {{ $stats['label_tahunan'] }}</span>
        <strong>{{ $stats['pembinaan_tahun'] }} / {{ $stats['target_tahun'] }}</strong>
    </div>
</div>
