@extends('layouts.user-app')

@php
    $backUrl = route('profil');
    $pageTitle = 'Edit Profil';
    $title = 'Edit Profil';
@endphp

@section('content')
<div class="card">
    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: var(--fic-text-primary);">Formulir Edit Profil</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Perbarui nama, email, dan komunitas Anda. Username dipakai untuk masuk dan tidak dapat diubah.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('profil.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">
                Nama <span class="required">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                placeholder="Nama tampilan Anda"
                class="form-input"
                maxlength="255"
                required
                autocomplete="name"
            >
        </div>

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input
                type="text"
                id="username"
                value="{{ $user->username }}"
                class="form-input"
                disabled
                autocomplete="username"
            >
            <span class="form-help">Username adalah kunci masuk dan tidak dapat diubah dari halaman ini.</span>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                placeholder="nama@contoh.id"
                class="form-input"
                maxlength="255"
                autocomplete="email"
            >
        </div>

        <div class="form-group">
            <label for="community_location" class="form-label">Komunitas</label>
            <input
                type="text"
                id="community_location"
                name="community_location"
                value="{{ old('community_location', $user->community_location) }}"
                placeholder="Contoh: Komunitas Bruderan Deltamas"
                class="form-input"
                maxlength="255"
                autocomplete="organization"
            >
            <span class="form-help">Lokasi komunitas atau penugasan. Kosongkan jika belum ditentukan.</span>
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 8px;">
            SIMPAN PROFIL
        </button>
    </form>
</div>
@endsection
