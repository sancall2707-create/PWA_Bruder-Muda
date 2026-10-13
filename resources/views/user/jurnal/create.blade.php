@extends('layouts.user-app')

@php
    $backUrl = route('jurnal.index');
    $pageTitle = 'Input Jurnal Baru';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Formulir Jurnal Pengembangan Diri</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Catat dan refleksikan kegiatan formasi, retret, studi, atau pelatihan yang Anda ikuti.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('jurnal.store') }}" method="POST">
        @csrf

        <!-- Hari / Tanggal Kegiatan -->
        <div class="form-group">
            <label for="activity_date" class="form-label">
                Hari / Tanggal Kegiatan <span class="required">*</span>
            </label>
            <input
                type="date"
                id="activity_date"
                name="activity_date"
                value="{{ old('activity_date', date('Y-m-d')) }}"
                class="form-input"
                required
            >
        </div>

        <!-- Nama Kegiatan -->
        <div class="form-group">
            <label for="activity_name" class="form-label">
                Nama Kegiatan <span class="required">*</span>
            </label>
            <input
                type="text"
                id="activity_name"
                name="activity_name"
                value="{{ old('activity_name') }}"
                placeholder="Contoh: Retret Tahunan Bruder Muda FIC"
                class="form-input"
                required
            >
        </div>

        <!-- Penyelenggara -->
        <div class="form-group">
            <label for="organizer" class="form-label">
                Penyelenggara <span class="required">*</span>
            </label>
            <input
                type="text"
                id="organizer"
                name="organizer"
                value="{{ old('organizer') }}"
                placeholder="Contoh: Dewan Pimpinan Provinsi FIC Indonesia"
                class="form-input"
                required
            >
        </div>

        <!-- Narasumber -->
        <div class="form-group">
            <label for="speaker" class="form-label">
                Narasumber / Fasilitator <span class="required">*</span>
            </label>
            <input
                type="text"
                id="speaker"
                name="speaker"
                value="{{ old('speaker') }}"
                placeholder="Contoh: Rm. Gregorius, SJ"
                class="form-input"
                required
            >
        </div>

        <!-- Deskripsi Kegiatan -->
        <div class="form-group">
            <label for="description" class="form-label">
                Deskripsi Kegiatan <span class="required">*</span>
            </label>
            <textarea
                id="description"
                name="description"
                class="form-textarea"
                rows="5"
                placeholder="Jelaskan ringkasan materi, dinamika kegiatan, atau pokok bahasan..."
                required
            >{{ old('description') }}</textarea>
        </div>

        <!-- Refleksi Singkat -->
        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label for="reflection" class="form-label">
                    Refleksi Singkat <span class="required">*</span>
                </label>
                <span id="charCounter" style="font-size: 11px; font-weight: 700; color: #667085;">0 / 200 - 500 karakter</span>
            </div>
            <textarea
                id="reflection"
                name="reflection"
                class="form-textarea"
                rows="6"
                placeholder="Bagaimana kegiatan ini mempengaruhi panggilan dan kehidupan rohani Anda sebagai Bruder FIC? (200–500 karakter)"
                required
            >{{ old('reflection') }}</textarea>
            <span class="form-help">Refleksi singkat minimal 200 karakter dan maksimal 500 karakter.</span>
        </div>

        <button type="submit" class="btn-primary" style="background-color: var(--fic-green); margin-top: 8px;">
            SIMPAN &amp; PUBLIKASI JURNAL
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.getElementById('reflection');
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
