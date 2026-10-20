@extends('layouts.super-admin')

@php
    $title = 'Profil & Akun Saya';
    $headerTitle = 'Profil & Edit Akun';
    $headerSubtitle = 'Kelola profil, foto, dan kata sandi Anda';
    $initials = 'SA';
    if ($user && !empty($user->name)) {
        $words = explode(' ', trim($user->name));
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }
    $hasPhoto = $user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo);
@endphp

@section('content')
<!-- Card 1: Avatar & Ringkasan Akun -->
<div class="card" style="padding: 24px 18px; text-align: center; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(14px); border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 8px 24px rgba(0, 62, 151, 0.08);">
    <div style="display: flex; justify-content: center; margin-bottom: 12px;">
        @if ($hasPhoto)
            <img src="{{ route('profil.foto') }}" alt="Foto profil {{ $user->name }}" width="92" height="92" style="width: 92px; height: 92px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 4px 16px rgba(20, 85, 192, 0.25); display: block;">
        @else
            <div style="width: 92px; height: 92px; border-radius: 50%; background: linear-gradient(135deg, #1455C0 0%, #003E97 100%); color: #ffffff; font-size: 30px; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(20, 85, 192, 0.25); border: 3px solid #ffffff;">
                {{ $initials }}
            </div>
        @endif
    </div>

    <h2 style="font-size: 18px; font-weight: 800; color: #101828; margin: 0 0 4px;">{{ $user->name }}</h2>
    <div style="font-size: 13px; color: #667085; margin-bottom: 10px;">{{ '@' . $user->username }}</div>

    <div>
        <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 12px; border-radius: 999px; background: rgba(20, 85, 192, 0.12); color: #1455C0; border: 1px solid rgba(20, 85, 192, 0.25);">
            {{ $user->role === 'super_admin' ? 'Super Admin FIC' : 'Admin TPPBM' }}
        </span>
    </div>
</div>

@if ($errors->any())
    <div style="background:#FEF3F2; border:1px solid #FECDCA; border-radius:14px; padding:12px 16px; color:#B42318; font-size:13px;">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Card 2: Form Edit Biodata & Foto Profil -->
<div class="card" style="padding: 20px 18px; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(14px); border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 8px 24px rgba(0, 62, 151, 0.08);">
    <h3 style="font-size: 14px; font-weight: 800; color: #1455C0; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #F2F4F7; padding-bottom: 8px; margin: 0 0 16px;">
        Informasi Data Diri
    </h3>

    <form method="POST" action="{{ route('superadmin.profil.update') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 14px;">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Foto Profil</label>
            <label for="profile_photo" class="file-upload-box" style="margin: 0; background: rgba(255, 255, 255, 0.7); border: 1.5px dashed #B2DDFF; border-radius: 12px; padding: 16px; text-align: center; cursor: pointer; display: block;">
                <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="#1455C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px; margin: 0 auto 6px; display: block;">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div style="font-size: 13px; font-weight: 700; color: #1455C0;">{{ $hasPhoto ? 'Ganti Foto Profil' : 'Pilih Foto Profil Baru' }}</div>
                <div style="font-size: 11px; color: #667085; margin-top: 2px;">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</div>
                <div id="saPhotoName" style="font-size: 12px; font-weight: 700; color: #027A48; margin-top: 4px;" hidden></div>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" style="display: none;">
            </label>

            @if ($hasPhoto)
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: #B42318; margin-top: 6px; cursor: pointer;">
                    <input type="checkbox" name="remove_photo" value="1">
                    Hapus foto saat ini (kembalikan ke inisial)
                </label>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Nama Lengkap <span style="color:#B42318;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Username Akses</label>
            <input type="text" value="{{ $user->username }}" readonly disabled style="padding: 12px 14px; border-radius: 12px; border: 1px solid #E4E7EC; background: #F2F4F7; color: #667085; font-size: 14px;">
            <span style="font-size: 11px; color: #667085;">Username adalah kunci login utama sistem dan bersifat permanen.</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Email Terdaftar</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="email@fic.local" maxlength="255" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">No. Telepon / WhatsApp</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" maxlength="30" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Komunitas / Kantor Tugas</label>
            <input type="text" name="community_location" value="{{ old('community_location', $user->community_location) }}" placeholder="Pusat Provinsi Semarang" maxlength="255" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Bio / Keterangan Tugas</label>
            <textarea name="bio" rows="3" placeholder="Deskripsi tugas atau catatan profil..." maxlength="1000" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px; font-family: inherit;">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <button type="submit" style="border: none; border-radius: 14px; padding: 14px 18px; font-weight: 800; font-size: 14px; background: linear-gradient(135deg, #1455C0 0%, #003E97 100%); color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(20, 85, 192, 0.25); margin-top: 4px;">
            SIMPAN PERUBAHAN PROFIL
        </button>
    </form>
</div>

<!-- Card 3: Form Ubah Kata Sandi Pribadi -->
<div class="card" style="padding: 20px 18px; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(14px); border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 8px 24px rgba(0, 62, 151, 0.08); margin-bottom: 30px;">
    <h3 style="font-size: 14px; font-weight: 800; color: #B57900; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #F2F4F7; padding-bottom: 8px; margin: 0 0 16px;">
        Ubah Kata Sandi Akun
    </h3>

    <form method="POST" action="{{ route('superadmin.profil.password') }}" style="display: flex; flex-direction: column; gap: 14px;">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Kata Sandi Saat Ini <span style="color:#B42318;">*</span></label>
            <input type="password" name="current_password" required placeholder="Masukkan kata sandi yang sedang digunakan" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Kata Sandi Baru <span style="color:#B42318;">*</span></label>
            <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter baru" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; font-weight: 700; color: #475467; text-transform: uppercase;">Konfirmasi Kata Sandi Baru <span style="color:#B42318;">*</span></label>
            <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi kata sandi baru" style="padding: 12px 14px; border-radius: 12px; border: 1px solid #D0D5DD; background: #fff; font-size: 14px;">
        </div>

        <button type="submit" style="border: none; border-radius: 14px; padding: 14px 18px; font-weight: 800; font-size: 14px; background: linear-gradient(135deg, #B57900 0%, #8D5D00 100%); color: #fff; cursor: pointer; box-shadow: 0 4px 12px rgba(181, 121, 0, 0.25); margin-top: 4px;">
            PERBARUI KATA SANDI
        </button>
    </form>
</div>

<script>
    var fileInput = document.getElementById('profile_photo');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var nameEl = document.getElementById('saPhotoName');
            var file = this.files && this.files[0];
            if (!file) {
                nameEl.hidden = true;
                nameEl.textContent = '';
                return;
            }
            nameEl.hidden = false;
            nameEl.textContent = 'Berkas dipilih: ' + file.name;
        });
    }
</script>
@endsection
