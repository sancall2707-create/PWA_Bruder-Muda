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
    <div style="position:relative;">
        <input type="text"
               id="filterBruderSearch"
               placeholder="Cari nama bruder pada pilihan..."
               autocomplete="off"
               style="width:100%; box-sizing:border-box; padding:8px 12px; margin-bottom:4px; border-radius:8px; border:1px solid #D0D5DD; background:#fff; font-size:12px; font-family:inherit;">
        <select id="selectBruderFilter" name="bruder" onchange="this.form.submit()" aria-label="Bruder" style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:13px;">
            <option value="">Semua Bruder</option>
            @foreach($bruders as $bruder)
                <option value="{{ $bruder->id }}" {{ (int) $filters['bruder'] === $bruder->id ? 'selected' : '' }}>{{ $bruder->name }}</option>
            @endforeach
        </select>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('filterBruderSearch');
    const select = document.getElementById('selectBruderFilter');
    if (!searchInput || !select) return;

    const originalOptions = Array.from(select.options).map(opt => ({
        value: opt.value,
        text: opt.text,
        selected: opt.selected
    }));

    searchInput.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        select.innerHTML = '';

        const matched = originalOptions.filter(opt =>
            opt.value === '' || opt.text.toLowerCase().includes(term)
        );

        if (matched.length === 0) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.text = 'Tidak ada nama bruder yang cocok';
            noOpt.disabled = true;
            select.appendChild(noOpt);
        } else {
            matched.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.value;
                el.text = opt.text;
                el.selected = opt.selected;
                select.appendChild(el);
            });
        }
    });
});
</script>

@if($exportKind === 'agenda')
    <div style="margin-top:12px;">
        <a href="{{ route('superadmin.monitoring.agenda.create') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:12px 14px; background:linear-gradient(135deg, #1455C0 0%, #003E97 100%); color:#fff; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none; box-shadow:0 4px 12px rgba(20,85,192,0.25); box-sizing:border-box;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Tambah Agenda</span>
        </a>
    </div>
@else
    <div style="display:flex; gap:8px; margin-top:12px;">
        @if($exportKind !== 'raport')
            <a href="{{ route('superadmin.monitoring.export', array_merge($queryBase, ['jenis' => $exportKind, 'format' => 'excel'])) }}" style="flex:1; text-align:center; padding:12px 8px; background:#B57900; color:#fff; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none;">Excel</a>
        @endif
        <a href="{{ route('superadmin.monitoring.export', array_merge($queryBase, ['jenis' => $exportKind, 'format' => 'pdf'])) }}" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-align:center; padding:12px 8px; background:#1455C0; color:#fff; border-radius:12px; font-weight:700; font-size:13px; text-decoration:none; box-sizing:border-box;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Unduh PDF
        </a>
        @if($exportKind === 'raport')
            <button id="btnPrintRaport" onclick="window.print()" style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:6px; background:#0E3366; color:#fff; border:none; padding:12px 8px; border-radius:12px; font-weight:700; font-size:13px; cursor:pointer; box-sizing:border-box; font-family:inherit;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak Raport
            </button>
        @endif
    </div>
@endif
