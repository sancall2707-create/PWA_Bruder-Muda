@extends('layouts.user-app')

@php
    $backUrl = route('agenda.index');
    $pageTitle = 'Jadwal Bimbingan Rohani';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Tambah Jadwal Bimbingan Rohani</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Atur jadwal pertemuan bersama Pembimbing Rohani Anda.
        </p>
    </div>

    <form action="{{ route('agenda.store') }}" method="POST">
        @csrf
        <input type="hidden" name="agenda_type" value="bimbingan">

        @if ($errors->any())
            <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Nama Pembimbing Rohani -->
        <div class="form-group">
            <label for="mentor_name" class="form-label">
                Nama Pembimbing Rohani <span class="required">*</span>
            </label>
            <input
                type="text"
                id="mentor_name"
                name="mentor_name"
                value="{{ old('mentor_name', 'Br. Yohanes Subagio, FIC') }}"
                class="form-input"
                required
            >
        </div>

        <!-- Hari & Tanggal Pertemuan -->
        <div class="form-group">
            <label for="meeting_date" class="form-label">
                Hari &amp; Tanggal Pertemuan <span class="required">*</span>
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

        <!-- Waktu Pertemuan -->
        <div class="form-group">
            <label for="meeting_time" class="form-label">
                Waktu Pertemuan <span class="required">*</span>
            </label>
            <input
                type="time"
                id="meeting_time"
                name="meeting_time"
                value="{{ old('meeting_time', '10:00') }}"
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

        <!-- Catatan / Topik Pembahasan (opsional) -->
        <div class="form-group">
            <label for="topic_notes" class="form-label">
                Catatan / Topik Pembahasan (Opsional)
            </label>
            <textarea
                id="topic_notes"
                name="topic_notes"
                class="form-textarea"
                rows="4"
                placeholder="Topik atau tema yang ingin didiskusikan dalam bimbingan..."
            >{{ old('topic_notes') }}</textarea>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 8px;">
            SIMPAN JADWAL BIMBINGAN
        </button>
    </form>
</div>
@endsection
