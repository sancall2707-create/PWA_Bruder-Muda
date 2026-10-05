@extends('layouts.user-app')

@php
    $backUrl = route('agenda.index');
    $pageTitle = 'Agenda Kegiatan Lainnya';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Tambah Agenda Kegiatan Lainnya</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Catat kegiatan komunitas, perutusan sekolah, atau agenda formatif lainnya.
        </p>
    </div>

    <form action="{{ route('agenda.store') }}" method="POST">
        @csrf
        <input type="hidden" name="agenda_type" value="lainnya">

        <!-- Nama / Judul Kegiatan -->
        <div class="form-group">
            <label for="title" class="form-label">
                Nama / Judul Kegiatan <span class="required">*</span>
            </label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                placeholder="Contoh: Pertemuan Komunitas Distrik"
                class="form-input"
                required
            >
        </div>

        <!-- Hari & Tanggal Pelaksanaan -->
        <div class="form-group">
            <label for="start_date" class="form-label">
                Hari &amp; Tanggal Pelaksanaan <span class="required">*</span>
            </label>
            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ old('start_date', date('Y-m-d')) }}"
                class="form-input"
                required
            >
        </div>

        <!-- Waktu Pelaksanaan -->
        <div class="form-group">
            <label for="start_time" class="form-label">
                Waktu Pelaksanaan <span class="required">*</span>
            </label>
            <input
                type="time"
                id="start_time"
                name="start_time"
                value="{{ old('start_time', '09:00') }}"
                class="form-input"
                required
            >
        </div>

        <!-- Lokasi / Tempat -->
        <div class="form-group">
            <label for="location" class="form-label">
                Lokasi / Tempat <span class="required">*</span>
            </label>
            <input
                type="text"
                id="location"
                name="location"
                value="{{ old('location') }}"
                placeholder="Contoh: Kapel Biara Pusat Semarang"
                class="form-input"
                required
            >
        </div>

        <!-- Deskripsi / Catatan Tambahan -->
        <div class="form-group">
            <label for="description" class="form-label">
                Deskripsi / Catatan Tambahan (Opsional)
            </label>
            <textarea
                id="description"
                name="description"
                class="form-textarea"
                rows="4"
                placeholder="Catatan persiapan, pakaian khusus, atau kebutuhan kegiatan..."
            >{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn-primary" style="background-color: #B57900; margin-top: 8px;">
            SIMPAN AGENDA BARU
        </button>
    </form>
</div>
@endsection
