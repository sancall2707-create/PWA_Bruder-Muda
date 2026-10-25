@extends('layouts.super-admin')

@section('content')
<div style="margin-bottom: 14px;">
    <a href="{{ route('superadmin.monitoring.agenda') }}" style="display:inline-flex; align-items:center; gap:6px; color:#1455C0; text-decoration:none; font-size:13px; font-weight:700;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        Kembali ke Rekapitulasi Agenda
    </a>
</div>

<div class="card" style="padding: 20px 18px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(14px); border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 8px 24px rgba(0, 62, 151, 0.08);">
    <div style="margin-bottom: 16px;">
        <h2 style="font-size: 16px; font-weight: 800; color: #101828; margin: 0 0 4px;">Tambah Agenda untuk Semua Bruder</h2>
        <p style="font-size: 12px; color: #667085; margin: 0; line-height: 1.5;">
            Agenda yang ditambahkan di sini akan secara otomatis masuk ke kalender dan daftar agenda <strong>seluruh akun Bruder Muda</strong>.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('superadmin.monitoring.agenda.store') }}" method="POST">
        @csrf

        <!-- Nama / Judul Kegiatan -->
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="title" class="form-label" style="display:block; font-size: 12px; font-weight: 700; color: #344054; margin-bottom: 6px;">
                Nama / Judul Kegiatan <span style="color: #D92D20;">*</span>
            </label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                placeholder="Contoh: Retret Tahunan Formasi Bruder Muda"
                class="form-input"
                required
                style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 14px; font-family: inherit;"
            >
        </div>

        <!-- Hari & Tanggal Pelaksanaan -->
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="start_date" class="form-label" style="display:block; font-size: 12px; font-weight: 700; color: #344054; margin-bottom: 6px;">
                Hari &amp; Tanggal Pelaksanaan <span style="color: #D92D20;">*</span>
            </label>
            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ old('start_date', date('Y-m-d')) }}"
                class="form-input"
                required
                style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 14px; font-family: inherit;"
            >
        </div>

        <!-- Waktu Pelaksanaan -->
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="start_time" class="form-label" style="display:block; font-size: 12px; font-weight: 700; color: #344054; margin-bottom: 6px;">
                Waktu Pelaksanaan <span style="color: #D92D20;">*</span>
            </label>
            <input
                type="time"
                id="start_time"
                name="start_time"
                value="{{ old('start_time', '09:00') }}"
                class="form-input"
                required
                style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 14px; font-family: inherit;"
            >
        </div>

        <!-- Lokasi / Tempat Pelaksanaan -->
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="location" class="form-label" style="display:block; font-size: 12px; font-weight: 700; color: #344054; margin-bottom: 6px;">
                Lokasi / Tempat Pelaksanaan <span style="color: #D92D20;">*</span>
            </label>
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location') }}"
                placeholder="Contoh: Kapel Pusat FIC Semarang"
                class="form-input"
                required
                style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 14px; font-family: inherit;"
            >
        </div>

        <!-- Deskripsi / Catatan Tambahan -->
        <div class="form-group" style="margin-bottom: 18px;">
            <label for="description" class="form-label" style="display:block; font-size: 12px; font-weight: 700; color: #344054; margin-bottom: 6px;">
                Deskripsi / Catatan Tambahan (Opsional)
            </label>
            <textarea
                id="description"
                name="description"
                rows="4"
                placeholder="Tuliskan keterangan tambahan atau perlengkapan yang perlu dibawa..."
                class="form-input"
                style="width: 100%; box-sizing: border-box; padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 14px; font-family: inherit; resize: vertical;"
            >{{ old('description') }}</textarea>
        </div>

        <!-- Kotak Info Otomatis -->
        <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1455C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div style="font-size: 12px; color: #1E40AF; line-height: 1.5;">
                Agenda ini akan otomatis terdistribusi dan muncul pada kalender agenda masing-masing Bruder Muda.
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; min-height: 46px; background: linear-gradient(135deg, #1455C0 0%, #003E97 100%); color: #fff; font-size: 14px; font-weight: 800; border-radius: 12px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(20, 85, 192, 0.25);">
            SIMPAN AGENDA UNTUK SEMUA BRUDER
        </button>
    </form>
</div>
@endsection
