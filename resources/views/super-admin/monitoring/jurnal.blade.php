@extends('layouts.super-admin')

@section('content')

{{-- Tab navigasi monitoring --}}
<div style="display:flex; background:#F2F4F7; border-radius:12px; padding:4px; gap:4px; margin-bottom:12px;">
    <a href="{{ route('superadmin.monitoring.raport') }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; color:#667085;">
        Raport
    </a>
    <a href="{{ route('superadmin.monitoring.index') }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; color:#667085;">
        Laporan
    </a>
    <a href="{{ route('superadmin.monitoring.agenda') }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; color:#667085;">
        Agenda
    </a>
    <a href="{{ route('superadmin.monitoring.jurnal.index') }}" style="flex:1; text-align:center; padding:10px 4px; border-radius:10px; font-weight:700; font-size:12px; text-decoration:none; background:#fff; color:#1455C0; box-shadow:0 2px 6px rgba(0,0,0,0.08);">
        Jurnal
    </a>
</div>

{{-- Filter Bruder --}}
<form method="GET" style="margin-bottom:12px;">
    <div style="display:flex; gap:8px; align-items:flex-end;">
        <div style="flex:1;">
            <label style="font-size:10px; font-weight:700; color:#667085; text-transform:uppercase; display:block; margin-bottom:4px;">Filter Bruder</label>
            <select name="bruder" onchange="this.form.submit()" style="width:100%; padding:9px 8px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:12px; font-weight:600;">
                <option value="">Semua Bruder</option>
                @foreach($bruders as $b)
                    <option value="{{ $b->id }}" {{ $selectedBruderId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        @if($selectedBruderId)
            <a href="{{ route('superadmin.monitoring.jurnal.index') }}" style="padding:9px 12px; border-radius:10px; border:1px solid #E4E7EC; background:#fff; font-size:12px; font-weight:700; color:#667085; text-decoration:none; white-space:nowrap;">
                Reset
            </a>
        @endif
    </div>
</form>

{{-- Notifikasi --}}
@if(session('success'))
    <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700; margin-bottom:10px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#FEF2F2; border:1px solid #FECDCA; color:#B42318; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700; margin-bottom:10px;">
        {{ session('error') }}
    </div>
@endif

{{-- Ringkasan jumlah --}}
<div style="font-size:12px; color:#667085; font-weight:600; margin-bottom:8px;">
    Total: <strong style="color:#101828;">{{ $journals->count() }}</strong> jurnal
    @if($selectedBruderId)
        dari <strong style="color:#1455C0;">{{ $bruders->firstWhere('id', $selectedBruderId)?->name ?? '' }}</strong>
    @endif
</div>

@if($journals->count() > 0)
{{-- Toolbar Pilih Semua --}}
<div style="display:flex; align-items:center; gap:10px; background:#EFF6FF; border:1px solid #BFDBFE; border-radius:12px; padding:10px 14px; margin-bottom:10px;">
    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; user-select:none; font-size:13px; font-weight:600; color:#1E293B;">
        <input type="checkbox" id="checkAllSA" style="width:17px; height:17px; accent-color:#1455C0; cursor:pointer; flex-shrink:0;">
        Pilih Semua
    </label>
    <span style="flex:1;"></span>
    <button id="btnHapusSA" onclick="konfirmasiHapusMassalSA()" disabled
        style="display:inline-flex; align-items:center; gap:6px; background:#DC2626; color:#fff; border:none; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; opacity:0.4; transition:opacity 0.2s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6l-1 14H6L5 6"></path>
            <path d="M10 11v6"></path><path d="M14 11v6"></path>
            <path d="M9 6V4h6v2"></path>
        </svg>
        <span id="labelHapusSA">Hapus Terpilih</span>
    </button>
</div>

{{-- Form hidden bulk delete --}}
<form id="formBulkSA" method="POST" action="{{ route('superadmin.monitoring.jurnal.destroyBulk') }}" style="display:none;">
    @csrf
    @method('DELETE')
    @if($selectedBruderId)
        <input type="hidden" name="bruder" value="{{ $selectedBruderId }}">
    @endif
    <div id="hiddenIdsSA"></div>
</form>
@endif

{{-- Daftar Jurnal --}}
<div style="display:flex; flex-direction:column; gap:12px;">
    @forelse($journals as $journal)
        @php
            $words = explode(' ', trim($journal->user->name ?? ''));
            $inisial = strtoupper(substr($words[0],0,1).(isset($words[1]) ? substr($words[1],0,1) : ''));
        @endphp
        <div style="background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; box-shadow:0 2px 6px rgba(16,24,40,0.03); position:relative;">
            {{-- Checkbox --}}
            <label style="position:absolute; top:14px; right:14px; cursor:pointer;">
                <input type="checkbox" class="sa-jurnal-cb" value="{{ $journal->id }}"
                    style="width:18px; height:18px; accent-color:#1455C0; cursor:pointer;"
                    onchange="updateToolbarSA()">
            </label>

            {{-- Identitas bruder --}}
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; padding-right:30px;">
                <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#1455C0,#003E97); color:#fff; font-size:13px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    {{ $inisial }}
                </div>
                <div>
                    <div style="font-size:13px; font-weight:700; color:#101828;">{{ $journal->user->name ?? '-' }}</div>
                    <div style="font-size:11px; color:#667085;">@{{ $journal->user->username ?? '' }}</div>
                </div>
            </div>

            {{-- Detail jurnal --}}
            <div style="font-weight:700; color:#1455C0; font-size:14px; margin-bottom:4px;">{{ $journal->activity_name }}</div>
            <div style="font-size:12px; color:#667085; margin-bottom:6px;">
                {{ $journal->organizer }} &bull; {{ $journal->speaker }}
                &bull; <strong>{{ $journal->activity_date->locale('id')->translatedFormat('j F Y') }}</strong>
            </div>
            @if($journal->reflection)
                <div style="background:#F8FAF9; border-left:3px solid #027A48; border-radius:0 8px 8px 0; padding:8px 10px; font-size:12px; color:#374151; line-height:1.5;">
                    {{ \Illuminate\Support\Str::limit($journal->reflection, 150) }}
                </div>
            @endif
        </div>
    @empty
        <div style="text-align:center; color:#667085; padding:32px; background:#fff; border:1px solid #E4E7EC; border-radius:14px; font-size:13px;">
            Belum ada jurnal sesuai filter.
        </div>
    @endforelse
</div>

{{-- Modal Konfirmasi --}}
<div id="modalSA" style="
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
        <div style="font-size:17px; font-weight:800; color:#1E293B; margin-bottom:8px;">Hapus Jurnal?</div>
        <div id="modalPesanSA" style="font-size:13px; color:#64748B; line-height:1.6; margin-bottom:24px;"></div>
        <div style="display:flex; gap:10px;">
            <button onclick="tutupModalSA()" style="flex:1; padding:11px; border-radius:10px; border:2px solid #E2E8F0; background:#fff; color:#475569; font-size:14px; font-weight:700; cursor:pointer;">Batal</button>
            <button onclick="eksekusiHapusSA()" style="flex:1; padding:11px; border-radius:10px; border:none; background:#DC2626; color:#fff; font-size:14px; font-weight:700; cursor:pointer;">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAllSA');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.sa-jurnal-cb').forEach(cb => cb.checked = this.checked);
            updateToolbarSA();
        });
    }
});

function updateToolbarSA() {
    const all      = document.querySelectorAll('.sa-jurnal-cb');
    const checked  = document.querySelectorAll('.sa-jurnal-cb:checked');
    const btn      = document.getElementById('btnHapusSA');
    const label    = document.getElementById('labelHapusSA');
    const checkAll = document.getElementById('checkAllSA');
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

function konfirmasiHapusMassalSA() {
    const checked = document.querySelectorAll('.sa-jurnal-cb:checked');
    if (!checked.length) return;
    document.getElementById('modalPesanSA').innerHTML =
        'Apakah Anda yakin ingin menghapus <strong>' + checked.length + ' jurnal</strong> yang dipilih?<br><br>' +
        '<span style="color:#DC2626;font-weight:600;">Data yang dihapus tidak dapat dipulihkan.</span>';
    tampilkanModalSA();
}

function eksekusiHapusSA() {
    tutupModalSA();
    const container = document.getElementById('hiddenIdsSA');
    container.innerHTML = '';
    document.querySelectorAll('.sa-jurnal-cb:checked').forEach(cb => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    document.getElementById('formBulkSA').submit();
}

function tampilkanModalSA() {
    document.getElementById('modalSA').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function tutupModalSA() {
    document.getElementById('modalSA').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('modalSA').addEventListener('click', function (e) {
    if (e.target === this) tutupModalSA();
});
</script>
@endsection
