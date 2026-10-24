@extends('layouts.super-admin')

@section('content')
@include('super-admin.monitoring._toolbar', ['exportKind' => 'laporan'])

@if(session('success'))
    <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700; margin-top:12px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:#FEF2F2; border:1px solid #FECDCA; color:#B42318; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700; margin-top:12px;">
        {{ session('error') }}
    </div>
@endif

{{-- Ringkasan & Toolbar Pilih Semua --}}
@if($reports->count() > 0)
    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px; margin-bottom:4px; font-size:12px; color:#667085; font-weight:600;">
        <div>Total: <strong style="color:#101828;">{{ $reports->count() }}</strong> laporan</div>
    </div>

    <div style="display:flex; align-items:center; gap:10px; background:#EFF6FF; border:1px solid #BFDBFE; border-radius:12px; padding:10px 14px; margin-bottom:12px;">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; user-select:none; font-size:13px; font-weight:600; color:#1E293B;">
            <input type="checkbox" id="checkAllReports" style="width:17px; height:17px; accent-color:#1455C0; cursor:pointer; flex-shrink:0;">
            Pilih Semua
        </label>
        <span style="flex:1;"></span>
        <button id="btnHapusReports" onclick="konfirmasiHapusMassalLaporan()" disabled
            style="display:inline-flex; align-items:center; gap:6px; background:#DC2626; color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; opacity:0.4; transition:opacity 0.2s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14H6L5 6"></path>
                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                <path d="M9 6V4h6v2"></path>
            </svg>
            <span id="labelHapusReports">Hapus Terpilih</span>
        </button>
    </div>

    {{-- Form hidden bulk delete --}}
    <form id="formBulkReports" method="POST" action="{{ route('superadmin.monitoring.laporan.destroyBulk') }}" style="display:none;">
        @csrf
        @method('DELETE')
        <div id="hiddenItemsContainer"></div>
    </form>
@endif

<div style="display:flex; flex-direction:column; gap:12px; {{ $reports->count() === 0 ? 'margin-top:14px;' : '' }}">
    @forelse($reports as $report)
        @php
            $itemKey = $report->category . ':' . $report->id;
        @endphp
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(16,24,40,0.03); position:relative;">
            {{-- Checkbox per kartu --}}
            @if($report->id > 0)
                <label style="position:absolute; top:14px; right:14px; cursor:pointer; z-index:2;">
                    <input type="checkbox" class="report-checkbox" value="{{ $itemKey }}"
                        style="width:18px; height:18px; accent-color:#1455C0; cursor:pointer;"
                        onchange="updateToolbarReports()">
                </label>
            @endif

            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; padding-right: {{ $report->id > 0 ? '30px' : '0' }};">
                <div style="flex:1;">
                    <div style="font-weight:700; color:#101828;">{{ $report->bruder }}</div>
                    <div style="font-size:12px; color:#667085; margin-top:2px;">{{ $report->type }} &bull; {{ $report->date_label }}</div>
                </div>
                <span style="padding:4px 10px; border-radius:999px; font-size:10px; font-weight:700; white-space:nowrap; {{ $report->status_done ? 'background:#ECFDF3;color:#027A48;border:1px solid #ABEFC6;' : 'background:#EFF8FF;color:#1455C0;border:1px solid #B2DDFF;' }}">{{ $report->status_label }}</span>
            </div>

            @if($report->counterpart !== '')
                <div style="margin-top:8px; font-size:12px; color:#344054;">{{ $report->counterpart }}</div>
            @endif

            @if($report->preview !== '')
                <div style="margin-top:8px; padding:10px; background:#F8FAF9; border-left:3px solid #1455C0; border-radius:0 8px 8px 0; font-size:12px; color:#374151;">{{ $report->preview }}</div>
            @endif

            {{-- Tombol Hapus per Item --}}
            @if($report->id > 0)
                <div style="display:flex; justify-content:flex-end; margin-top:12px; padding-top:10px; border-top:1px dashed #E4E7EC;">
                    <button type="button"
                        onclick="konfirmasiHapusSatuLaporan('{{ $report->category }}', {{ $report->id }}, '{{ addslashes($report->type) }} milik {{ addslashes($report->bruder) }} ({{ $report->date_label }})')"
                        style="display:inline-flex; align-items:center; gap:6px; background:#FEF2F2; color:#B42318; border:1px solid #FECDCA; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14H6L5 6"></path>
                            <path d="M10 11v6"></path><path d="M14 11v6"></path>
                            <path d="M9 6V4h6v2"></path>
                        </svg>
                        Hapus
                    </button>
                    {{-- Form hidden hapus satu item --}}
                    <form id="form-hapus-report-{{ $report->category }}-{{ $report->id }}"
                          method="POST"
                          action="{{ route('superadmin.monitoring.laporan.destroySingle', ['type' => $report->category, 'id' => $report->id]) }}"
                          style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:24px; background:#fff; border:1px solid #E4E7EC; border-radius:14px;">Belum ada laporan sesuai filter.</div>
    @endforelse
</div>

{{-- Modal Konfirmasi Hapus Laporan --}}
<div id="modalLaporan" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);
    align-items:center; justify-content:center; padding:20px;
">
    <div style="background:#fff; border-radius:18px; padding:28px 22px; max-width:380px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.25); text-align:center;">
        <div style="width:56px; height:56px; background:#FEF2F2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14H6L5 6"></path>
                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                <path d="M9 6V4h6v2"></path>
            </svg>
        </div>
        <div style="font-size:17px; font-weight:800; color:#1E293B; margin-bottom:8px;">Hapus Laporan?</div>
        <div id="modalPesanLaporan" style="font-size:13px; color:#64748B; line-height:1.6; margin-bottom:24px;"></div>
        <div style="display:flex; gap:10px;">
            <button onclick="tutupModalLaporan()" style="flex:1; padding:11px; border-radius:10px; border:2px solid #E2E8F0; background:#fff; color:#475569; font-size:14px; font-weight:700; cursor:pointer;">Batal</button>
            <button onclick="eksekusiHapusLaporan()" style="flex:1; padding:11px; border-radius:10px; border:none; background:#DC2626; color:#fff; font-size:14px; font-weight:700; cursor:pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let _modeLaporan = null; // 'satu' | 'massal'
let _targetFormIdLaporan = null;

document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAllReports');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.report-checkbox').forEach(cb => cb.checked = this.checked);
            updateToolbarReports();
        });
    }
});

function updateToolbarReports() {
    const all      = document.querySelectorAll('.report-checkbox');
    const checked  = document.querySelectorAll('.report-checkbox:checked');
    const btn      = document.getElementById('btnHapusReports');
    const label    = document.getElementById('labelHapusReports');
    const checkAll = document.getElementById('checkAllReports');
    if (!btn) return;
    const ada = checked.length > 0;
    btn.disabled = !ada;
    btn.style.opacity = ada ? '1' : '0.4';
    label.textContent = ada ? `Hapus ${checked.length} Terpilih` : 'Hapus Terpilih';
    if (checkAll) {
        checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        checkAll.checked = all.length > 0 && checked.length === all.length;
    }
}

function konfirmasiHapusSatuLaporan(cat, id, label) {
    _modeLaporan = 'satu';
    _targetFormIdLaporan = 'form-hapus-report-' + cat + '-' + id;
    document.getElementById('modalPesanLaporan').innerHTML =
        'Apakah Anda yakin ingin menghapus laporan:<br><strong>' + label + '</strong>?<br><br>' +
        '<span style="color:#DC2626;font-weight:600;">Data yang dihapus tidak dapat dipulihkan.</span>';
    tampilkanModalLaporan();
}

function konfirmasiHapusMassalLaporan() {
    const checked = document.querySelectorAll('.report-checkbox:checked');
    if (!checked.length) return;
    _modeLaporan = 'massal';
    document.getElementById('modalPesanLaporan').innerHTML =
        'Apakah Anda yakin ingin menghapus <strong>' + checked.length + ' laporan</strong> yang dipilih?<br><br>' +
        '<span style="color:#DC2626;font-weight:600;">Data yang dihapus tidak dapat dipulihkan.</span>';
    tampilkanModalLaporan();
}

function eksekusiHapusLaporan() {
    tutupModalLaporan();
    if (_modeLaporan === 'satu' && _targetFormIdLaporan) {
        const f = document.getElementById(_targetFormIdLaporan);
        if (f) f.submit();
    } else if (_modeLaporan === 'massal') {
        const container = document.getElementById('hiddenItemsContainer');
        container.innerHTML = '';
        document.querySelectorAll('.report-checkbox:checked').forEach(cb => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'items[]';
            inp.value = cb.value;
            container.appendChild(inp);
        });
        document.getElementById('formBulkReports').submit();
    }
}

function tampilkanModalLaporan() {
    const m = document.getElementById('modalLaporan');
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function tutupModalLaporan() {
    const m = document.getElementById('modalLaporan');
    m.style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('modalLaporan').addEventListener('click', function (e) {
    if (e.target === this) tutupModalLaporan();
});
</script>
@endsection
