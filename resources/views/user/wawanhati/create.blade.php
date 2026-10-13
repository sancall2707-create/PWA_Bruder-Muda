@extends('layouts.user-app')

@php
    $backUrl = route('wawanhati.index');
    $pageTitle = 'Input Laporan Wawanhati';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Formulir Laporan Wawanhati Pimpinan Lokal</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Isi laporan berkala dialog pembinaan bersama Pimpinan Komunitas Lokal (Piko).
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('wawanhati.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Hari / Tanggal Pertemuan -->
        <div class="form-group">
            <label for="meeting_date" class="form-label">
                Hari / Tanggal Pertemuan <span class="required">*</span>
            </label>
            <input
                type="date"
                id="meeting_date"
                name="meeting_date"
                value="{{ old('meeting_date', date('Y-m-d')) }}"
                class="form-input"
                required
            >
        </div>

        <!-- Nama Pimpinan Lokal -->
        <div class="form-group">
            <label for="local_leader_name" class="form-label">
                Nama Pimpinan Lokal (Piko) <span class="required">*</span>
            </label>
            <input
                type="text"
                id="local_leader_name"
                name="local_leader_name"
                value="{{ old('local_leader_name', 'Br. Fransiskus Xaverius, FIC') }}"
                placeholder="Contoh: Br. Fransiskus Xaverius, FIC"
                class="form-input"
                required
            >
        </div>

        <!-- Tempat / Ruangan -->
        <div class="form-group">
            <label for="location" class="form-label">
                Tempat / Ruangan <span class="required">*</span>
            </label>
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location') }}"
                placeholder="Contoh: Ruang Pimpinan Komunitas Solo"
                class="form-input"
                required
            >
        </div>

        <!-- Isi / Catatan Wawanhati -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label for="report_content" class="form-label">
                    Isi / Catatan Wawanhati <span class="required">*</span>
                </label>
                <span id="charCounter" style="font-size: 11px; font-weight: 700; color: #667085;">0 / 200 - 500 karakter</span>
            </div>
            <textarea
                id="report_content"
                name="report_content"
                class="form-textarea"
                rows="6"
                placeholder="Tuliskan catatan dialog seputar kehidupan komunitas, karya kerasulan, dan hal-hal pembinaan (200–500 karakter)..."
                minlength="200"
                maxlength="500"
                required
            >{{ old('report_content') }}</textarea>
            <span class="form-help">Wawanhati adalah dialog persaudaraan dan evaluasi pembinaan bersama Piko. Minimal 200 karakter, maksimal 500 karakter.</span>
        </div>

        <!-- Upload Bukti Logbook / Catatan Fisik -->
        <div class="form-group">
            <label class="form-label">
                Upload Bukti Logbook / Catatan Fisik (Opsional)
            </label>
            <label for="proof_file" class="file-upload-box">
                <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div class="file-upload-text">Klik atau pilih foto / dokumen bukti wawanhati</div>
                <div class="file-upload-subtext">Format: JPG, PNG, atau PDF (Maks. 5MB)</div>
                <input
                    type="file"
                    id="proof_file"
                    name="proof_file"
                    accept="image/*,.pdf"
                    style="display: none;"
                    onchange="if(this.files[0]) document.getElementById('selectedFileName').innerText = this.files[0].name"
                >
            </label>
            <div id="selectedFileName" style="font-size: 12px; color: var(--fic-primary); font-weight: 600; margin-top: 4px;"></div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary" style="background-color: var(--fic-cyan); margin-top: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
            SIMPAN &amp; KIRIM LAPORAN
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('report_content');
        const counter = document.getElementById('charCounter');
        if (textarea && counter) {
            function updateCounter() {
                const len = textarea.value.length;
                counter.innerText = `${len} / 200 - 500 karakter`;
                if (len < 200) {
                    counter.style.color = '#B42318'; // merah jika kurang
                } else if (len > 500) {
                    counter.style.color = '#B42318'; // merah jika lebih
                } else {
                    counter.style.color = '#027A48'; // hijau jika pas 200-500
                }
            }
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endsection
