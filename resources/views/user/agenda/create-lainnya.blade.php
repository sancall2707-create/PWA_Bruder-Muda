@extends('layouts.user-app')

@php
    $editing = isset($schedule);
    $backUrl = route('agenda.index');
    $pageTitle = $editing ? 'Ubah Agenda Kegiatan Lainnya' : 'Agenda Kegiatan Lainnya';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">{{ $editing ? 'Ubah Agenda Kegiatan Lainnya' : 'Tambah Agenda Kegiatan Lainnya' }}</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Catat kegiatan komunitas, perutusan sekolah, atau agenda formatif lainnya.
        </p>
    </div>

    <form action="{{ $editing ? route('agenda.update', ['type' => 'lainnya', 'schedule' => $schedule]) : route('agenda.store') }}" method="POST">
        @csrf
        @if($editing)
            @method('PUT')
        @else
            <input type="hidden" name="agenda_type" value="lainnya">
        @endif

        @if ($errors->any())
            <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Nama / Judul Kegiatan -->
        <div class="form-group">
            <label for="title" class="form-label">
                Nama / Judul Kegiatan <span class="required">*</span>
            </label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $editing ? $schedule->title : '') }}"
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
                value="{{ old('start_date', $editing ? $schedule->start_at->format('Y-m-d') : date('Y-m-d')) }}"
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
                value="{{ old('start_time', $editing ? $schedule->start_at->format('H:i') : '09:00') }}"
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
                value="{{ old('location', $editing ? $schedule->location : '') }}"
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
            >{{ old('description', $editing ? $schedule->description : '') }}</textarea>
        </div>

        <button type="submit" class="btn-primary" style="background-color: #B57900; margin-top: 8px;">
            {{ $editing ? 'SIMPAN PERUBAHAN' : 'SIMPAN AGENDA BARU' }}
        </button>
    </form>
</div>
@endsection
