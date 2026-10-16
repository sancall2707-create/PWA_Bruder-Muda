@extends('layouts.user-app')

@php
    $backUrl = route('profil');
    $pageTitle = 'Ubah Kata Sandi';
    $title = 'Ubah Kata Sandi';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Formulir Ubah Kata Sandi</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Masukkan kata sandi saat ini, lalu kata sandi baru minimal 8 karakter yang berbeda dari sandi lama. Sesi masuk Anda tetap aktif.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('profil.kata-sandi.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="current_password" class="form-label">
                Kata Sandi Saat Ini <span class="required">*</span>
            </label>
            <input
                type="password"
                id="current_password"
                name="current_password"
                class="form-input"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="form-group">
            <label for="password" class="form-label">
                Kata Sandi Baru <span class="required">*</span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                required
                minlength="8"
                autocomplete="new-password"
            >
            <span class="form-help">Minimal 8 karakter dan harus berbeda dari kata sandi saat ini.</span>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                Konfirmasi Kata Sandi Baru <span class="required">*</span>
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-input"
                required
                minlength="8"
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 8px;">
            SIMPAN KATA SANDI
        </button>
    </form>
</div>
@endsection
