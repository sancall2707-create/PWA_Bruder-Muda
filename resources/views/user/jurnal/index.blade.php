@extends('layouts.user-app')

@php
    $backUrl = route('dashboard');
    $pageTitle = 'Jurnal Pengembangan Diri';
@endphp

@section('content')
<!-- Header Summary Card -->
<div class="card" style="padding: 16px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 11px; font-weight: 700; color: var(--fic-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                Refleksi &amp; Formasi
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--fic-text-primary); margin-top: 2px;">
                Catatan Pembinaan Berkelanjutan
            </div>
        </div>
        <span class="badge badge-done">Pertumbuhan Diri</span>
    </div>
</div>

@if(session('success'))
    <div style="background-color: var(--fic-green-soft); border: 1px solid rgba(53, 168, 102, 0.3); color: #027A48; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: #FEF2F2; border: 1px solid rgba(220, 38, 38, 0.3); color: #DC2626; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">
        {{ session('error') }}
    </div>
@endif

<!-- Primary CTA -->
<a href="{{ route('jurnal.create') }}" class="btn-primary" style="background-color: var(--fic-green);">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    TAMBAH JURNAL BARU
</a>

@if($journals->count() > 0)
<!-- Toolbar Pilih & Hapus Massal -->
<div id="selectionToolbar" style="display: flex; align-items: center; gap: 10px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 10px 14px;">
    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; font-size: 13px; font-weight: 600; color: var(--fic-text-primary);">
        <input type="checkbox" id="checkAll" style="width: 17px; height: 17px; accent-color: #1455C0; cursor: pointer; flex-shrink: 0;">
        Pilih Semua
    </label>
    <span style="flex: 1;"></span>
    <button id="btnHapusTerpilih" onclick="konfirmasiHapusMassal()" disabled
        style="display: inline-flex; align-items: center; gap: 6px; background: #DC2626; color: #fff; border: none; border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 700; cursor: pointer; opacity: 0.4; transition: opacity 0.2s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6l-1 14H6L5 6"></path>
            <path d="M10 11v6"></path><path d="M14 11v6"></path>
            <path d="M9 6V4h6v2"></path>
        </svg>
        <span id="labelHapus">Hapus Terpilih</span>
    </button>
</div>

<!-- Form hidden untuk bulk delete -->
<form id="formBulkDelete" method="POST" action="{{ route('jurnal.destroyBulk') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="hiddenIdsContainer"></div>
</form>
@endif

<!-- List of Journals -->
<div style="display: flex; flex-direction: column; gap: 14px;">
    @forelse($journals as $journal)
        <div class="item-card" style="position: relative;">
            <!-- Checkbox pilih -->
            <label style="position: absolute; top: 14px; right: 14px; display: flex; align-items: center; cursor: pointer; z-index: 2;">
                <input
                    type="checkbox"
                    class="jurnal-checkbox"
                    value="{{ $journal->id }}"
                    style="width: 18px; height: 18px; accent-color: #1455C0; cursor: pointer;"
                    onchange="updateToolbar()"
                >
            </label>

            <div class="item-card-header" style="padding-right: 34px;">
                <div>
                    <div class="item-card-title">{{ $journal->activity_name }}</div>
                    <div style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 2px;">
                        {{ $journal->organizer }} &bull; {{ $journal->speaker }}
                    </div>
                </div>
                <x-status-badge :status="$journal->status" />
            </div>

            <div class="item-card-meta">
                <div class="meta-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                    </svg>
                    <span>Tanggal: <strong>{{ $journal->activity_date->locale('id')->translatedFormat('j F Y') }}</strong></span>
                </div>
            </div>

            @if(!empty($journal->reflection))
                <div class="item-card-preview" style="background-color: #F8FAF9; border-left: 3px solid var(--fic-green);">
                    <strong style="display: block; font-size: 11px; text-transform: uppercase; color: #027A48; margin-bottom: 4px;">
                        Refleksi Singkat:
                    </strong>
                    {{ $journal->reflection }}
                </div>
            @endif

            <div style="display: flex; gap: 8px; margin-top: 12px;">
                <a href="{{ route('jurnal.edit', $journal) }}" class="btn-secondary" style="flex: 1; min-height: 40px; padding: 8px 12px; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                    Edit
                </a>
                <button type="button"
                    onclick="konfirmasiHapusSatu({{ $journal->id }}, '{{ addslashes($journal->activity_name) }}')"
                    class="btn-danger" style="flex: 1; min-height: 40px; padding: 8px 12px; font-size: 13px; border: none; cursor: pointer;">
                    Hapus
                </button>
                <!-- Form hidden hapus satu -->
                <form id="form-hapus-{{ $journal->id }}" method="POST" action="{{ route('jurnal.destroy', $journal) }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    @empty
        <x-empty-state
            title="Belum ada jurnal pengembangan diri"
            message="Dokumentasikan pelatihan, retret, dan refleksi rohani Anda."
        />
    @endforelse
</div>

<!-- Modal Konfirmasi -->
<div id="modalKonfirmasi" style="
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    align-items: center; justify-content: center; padding: 20px;
">
    <div style="
        background: #fff; border-radius: 18px; padding: 28px 22px;
        max-width: 380px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        text-align: center;
    ">
        <div style="width: 56px; height: 56px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14H6L5 6"></path>
                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                <path d="M9 6V4h6v2"></path>
            </svg>
        </div>
        <div style="font-size: 17px; font-weight: 800; color: #1E293B; margin-bottom: 8px;">Hapus Jurnal?</div>
        <div id="modalPesan" style="font-size: 13px; color: #64748B; line-height: 1.6; margin-bottom: 24px;"></div>
        <div style="display: flex; gap: 10px;">
            <button onclick="tutupModal()" style="
                flex: 1; padding: 11px; border-radius: 10px; border: 2px solid #E2E8F0;
                background: #fff; color: #475569; font-size: 14px; font-weight: 700; cursor: pointer;
            ">Batal</button>
            <button id="btnKonfirmasiHapus" onclick="eksekusiHapus()" style="
                flex: 1; padding: 11px; border-radius: 10px; border: none;
                background: #DC2626; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer;
            ">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
// ── State ──────────────────────────────────────────────────────────────────
let _modePilihan = null; // 'satu' | 'massal'
let _hapusSatuId = null;

// ── Toolbar: pilih semua / update counter ──────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.jurnal-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateToolbar();
        });
    }
});

function updateToolbar() {
    const checkboxes = document.querySelectorAll('.jurnal-checkbox');
    const terpilih   = document.querySelectorAll('.jurnal-checkbox:checked');
    const btn        = document.getElementById('btnHapusTerpilih');
    const label      = document.getElementById('labelHapus');
    const checkAll   = document.getElementById('checkAll');

    if (btn) {
        const ada = terpilih.length > 0;
        btn.disabled  = !ada;
        btn.style.opacity = ada ? '1' : '0.4';
        label.textContent = ada ? `Hapus ${terpilih.length} Terpilih` : 'Hapus Terpilih';
    }
    if (checkAll) {
        checkAll.indeterminate = terpilih.length > 0 && terpilih.length < checkboxes.length;
        if (checkboxes.length > 0) {
            checkAll.checked = terpilih.length === checkboxes.length;
        }
    }
}

// ── Konfirmasi hapus satu ──────────────────────────────────────────────────
function konfirmasiHapusSatu(id, nama) {
    _modePilihan = 'satu';
    _hapusSatuId = id;
    document.getElementById('modalPesan').innerHTML =
        'Apakah Anda yakin ingin menghapus jurnal:<br><strong>"' + nama + '"</strong>?<br><br>' +
        '<span style="color:#DC2626; font-weight:600;">Data yang dihapus tidak dapat dipulihkan.</span>';
    tampilkanModal();
}

// ── Konfirmasi hapus massal ────────────────────────────────────────────────
function konfirmasiHapusMassal() {
    const terpilih = document.querySelectorAll('.jurnal-checkbox:checked');
    if (terpilih.length === 0) return;

    _modePilihan = 'massal';
    document.getElementById('modalPesan').innerHTML =
        'Apakah Anda yakin ingin menghapus <strong>' + terpilih.length + ' jurnal</strong> yang dipilih?<br><br>' +
        '<span style="color:#DC2626; font-weight:600;">Data yang dihapus tidak dapat dipulihkan.</span>';
    tampilkanModal();
}

// ── Eksekusi setelah konfirmasi ────────────────────────────────────────────
function eksekusiHapus() {
    tutupModal();
    if (_modePilihan === 'satu' && _hapusSatuId) {
        document.getElementById('form-hapus-' + _hapusSatuId).submit();
    } else if (_modePilihan === 'massal') {
        const container = document.getElementById('hiddenIdsContainer');
        container.innerHTML = '';
        document.querySelectorAll('.jurnal-checkbox:checked').forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        document.getElementById('formBulkDelete').submit();
    }
}

// ── Modal helpers ──────────────────────────────────────────────────────────
function tampilkanModal() {
    const modal = document.getElementById('modalKonfirmasi');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function tutupModal() {
    const modal = document.getElementById('modalKonfirmasi');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
// Tutup modal jika klik luar area
document.getElementById('modalKonfirmasi').addEventListener('click', function (e) {
    if (e.target === this) tutupModal();
});
</script>
@endsection
