@php
    $queryBase = array_filter([
        'bulan' => $filters['bulan'],
        'bulan_akhir' => $filters['bulan_akhir'] ?? $filters['bulan'],
        'tahun' => $filters['tahun'],
        'bruder' => $filters['bruder'],
    ], fn ($value) => $value !== null && $value !== '');
    $exportKind = $exportKind ?? 'laporan';
@endphp

<div style="display: flex; background: #F2F4F7; border-radius: 12px; padding: 4px; gap: 4px;">
    <a href="{{ route('superadmin.monitoring.raport', $queryBase) }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.raport') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Raport
    </a>
    <a href="{{ route('superadmin.monitoring.index', $queryBase) }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.index') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Laporan
    </a>
    <a href="{{ route('superadmin.monitoring.agenda', $queryBase) }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.agenda') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Agenda
    </a>
    <a href="{{ route('superadmin.monitoring.jurnal.index') }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; {{ request()->routeIs('superadmin.monitoring.jurnal.*') ? 'background:#fff;color:#1455C0;box-shadow:0 2px 6px rgba(0,0,0,0.08);' : 'color:#667085;' }}">
        Jurnal
    </a>
</div>

<form method="GET" style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
    @if(!empty($tampilan))
        <input type="hidden" name="tampilan" value="{{ $tampilan }}">
    @endif
    <div style="display:flex; gap:6px; align-items:center;">
        <div style="flex:1; display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:10px; font-weight:700; color:#667085; text-transform:uppercase;">Dari Bulan</label>
            <select name="bulan" onchange="this.form.submit()" aria-label="Bulan Awal" style="width:100%; padding:9px 8px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:12px; font-weight:600;">
                @for($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}" {{ (int) $filters['bulan'] === $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div style="flex:1; display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:10px; font-weight:700; color:#667085; text-transform:uppercase;">Sampai Bulan</label>
            <select name="bulan_akhir" onchange="this.form.submit()" aria-label="Bulan Akhir" style="width:100%; padding:9px 8px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:12px; font-weight:600;">
                @for($month = 1; $month <= 12; $month++)
                    <option value="{{ $month }}" {{ (int) ($filters['bulan_akhir'] ?? $filters['bulan']) === $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $month, 1)->locale('id')->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div style="width:84px; display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:10px; font-weight:700; color:#667085; text-transform:uppercase;">Tahun</label>
            <select name="tahun" onchange="this.form.submit()" aria-label="Tahun" style="width:100%; padding:9px 8px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:12px; font-weight:600;">
                @foreach(range(now()->year - 1, now()->year + 1) as $year)
                    <option value="{{ $year }}" {{ (int) $filters['tahun'] === $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <select name="bruder" onchange="this.form.submit()" aria-label="Bruder" style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:13px;">
        <option value="">Semua Bruder</option>
        @foreach($bruders as $bruder)
            <option value="{{ $bruder->id }}" {{ (int) $filters['bruder'] === $bruder->id ? 'selected' : '' }}>{{ $bruder->name }}</option>
        @endforeach
    </select>
</form>

<div style="display:flex; gap:8px; margin-top:12px;">
    @if($exportKind !== 'raport')
        <a href="{{ route('superadmin.monitoring.export', array_merge($queryBase, ['jenis' => $exportKind, 'format' => 'excel'])) }}" style="flex:1; text-align:center; padding:12px 8px; background:#B57900; color:#fff; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none;">Excel</a>
    @endif
    <a href="{{ route('superadmin.monitoring.export', array_merge($queryBase, ['jenis' => $exportKind, 'format' => 'pdf'])) }}" style="flex:1; text-align:center; padding:12px 8px; background:#1455C0; color:#fff; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none;">PDF</a>
</div>
