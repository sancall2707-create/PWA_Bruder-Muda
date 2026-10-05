@extends('layouts.user-app')

@php
    $backUrl = route('bimbingan.index');
    $pageTitle = 'Input Laporan Bimbingan';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Formulir Laporan Bimbingan Rohani</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Isi laporan setelah pelaksanaan bimbingan rohani bersama pembimbing.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('bimbingan.store') }}" method="POST" enctype="multipart/form-data">
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

        <!-- Nama Pembimbing -->
        <div class="form-group">
            <label for="mentor_name" class="form-label">
                Nama Pembimbing Rohani <span class="required">*</span>
            </label>
            <input
                type="text"
                id="mentor_name"
                name="mentor_name"
                value="{{ old('mentor_name', 'Br. Yohanes Subagio, FIC') }}"
                placeholder="Contoh: Br. Yohanes Subagio, FIC"
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
                placeholder="Contoh: Ruang Bimbingan Ambarawa"
                class="form-input"
                required
            >
        </div>

        <!-- Isi Bimbingan / Catatan -->
        <div class="form-group">
            <label for="report_content" class="form-label">
                Isi Bimbingan / Catatan Refleksi <span class="required">*</span>
            </label>
            <textarea
                id="report_content"
                name="report_content"
                class="form-textarea"
                rows="6"
                placeholder="Tuliskan poin-poin refleksi, arahan pembimbing, dan resolusi hidup rohani..."
                required
            >{{ old('report_content') }}</textarea>
            <span class="form-help">Catatan bimbingan bersifat rahasia dan dipergunakan untuk pendampingan formatif.</span>
        </div>

        <!-- Upload Bukti Logbook / Catatan Fisik (UI Phase) -->
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
                <div class="file-upload-text">Klik atau pilih foto / dokumen logbook</div>
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
        <button type="submit" class="btn-primary" style="margin-top: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
            SIMPAN &amp; KIRIM LAPORAN
        </button>
    </form>
</div>
@endsection
