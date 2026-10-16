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
            Perbarui nama, email, komunitas, dan foto profil Anda. Username dipakai untuk masuk dan tidak dapat diubah.
        </p>
    </div>

    @if ($errors->any())
        <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 18px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="Foto profil saat ini" width="76" height="76" style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 4px 12px rgba(20, 85, 192, 0.25);">
            @endif
            <label for="profile_photo" class="file-upload-box">
                <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div class="file-upload-text">{{ $photoUrl ? 'Ganti Foto Profil' : 'Tambahkan Foto Profil' }}</div>
                <div class="file-upload-subtext">JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.</div>
                <div class="file-upload-filename" id="profilePhotoName" hidden></div>
                <input
                    type="file"
                    id="profile_photo"
                    name="profile_photo"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="file-upload-input"
                >
            </label>
            <span class="form-help">Foto bersifat opsional. Menyimpan profil tanpa memilih berkas tidak menghapus foto yang sudah ada.</span>
        </div>

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

@push('scripts')
<script>
    document.getElementById('profile_photo').addEventListener('change', function () {
        var nameEl = document.getElementById('profilePhotoName');
        var file = this.files && this.files[0];

        if (!file) {
            nameEl.hidden = true;
            nameEl.textContent = '';
            return;
        }

        nameEl.hidden = false;
        nameEl.textContent = file.name;
    });
</script>
@endpush
