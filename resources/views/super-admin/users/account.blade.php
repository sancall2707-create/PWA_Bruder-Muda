<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Kelola Akun &amp; Data Diri - PWA BRUDER FIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/fic-pwa.css">
    <style>
        body { background:#e7f0fb; background-image:linear-gradient(180deg,#d5e4f8 0%,#eef3fb 46%,#f7f9fc 100%); font-family:'Plus Jakarta Sans','Inter',sans-serif; margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; }
        .container { width:100%; max-width:480px; background:linear-gradient(180deg,rgba(20,85,192,0.1) 0%,rgba(234,243,255,0.55) 180px,rgba(247,249,252,0.88) 100%); min-height:100vh; display:flex; flex-direction:column; box-shadow:0 0 30px rgba(0,62,151,0.08); padding-bottom:72px; box-sizing:border-box; }
        .header { background:linear-gradient(135deg,rgba(0,62,151,0.9) 0%,rgba(20,85,192,0.78) 100%); color:#fff; padding:20px; border-bottom:1px solid rgba(255,255,255,0.28); border-bottom-left-radius:20px; border-bottom-right-radius:20px; box-shadow:0 8px 20px rgba(0,62,151,0.12); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); }
        .header-nav { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .btn-header-back { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0; line-height:0; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.3); border-radius:10px; color:#fff; text-decoration:none; }
        .btn-header-back svg { display:block; }
        .badge { display:inline-flex; gap:6px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; text-transform:uppercase; }
        .title { font-size:20px; font-weight:800; margin:0 0 4px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 96px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .form-section { background:rgba(255,255,255,0.66); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-radius:16px; border:1px solid rgba(255,255,255,0.84); padding:18px; box-shadow:0 8px 24px rgba(0,62,151,0.08); display:flex; flex-direction:column; gap:14px; }
        .section-label { font-size:12px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin-bottom:4px; }
        .field { display:flex; flex-direction:column; gap:6px; }
        .field label { font-size:12px; font-weight:700; color:#475467; text-transform:uppercase; letter-spacing:0.4px; }
        .field input, .field select, .field textarea { width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.84); font-size:14px; background:rgba(255,255,255,0.72); backdrop-filter:blur(12px); font-family:inherit; color:#101828; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color:#1455C0; outline:none; box-shadow:0 0 0 3px rgba(20,85,192,0.12); }
        .field input[readonly] { background:rgba(242,244,247,0.85); color:#475467; cursor:default; }
        .hint { margin:0; font-size:12px; line-height:1.45; color:#667085; }
        .btn-submit { border:none; border-radius:14px; padding:15px; font-weight:800; font-size:15px; background:linear-gradient(135deg, #1455C0 0%, #003E97 100%); color:#fff; cursor:pointer; box-shadow:0 4px 12px rgba(20,85,192,0.25); width:100%; }
        .back-link { display:block; text-align:center; margin-top:8px; color:#1455C0; text-decoration:none; font-weight:700; font-size:13px; }
    </style>
</head>
<body>
@php
    $family = $user->family_data ?? [];
    $education = $user->education_data ?? [];
    $formation = $user->formation_data ?? [];
    $assignment = $user->assignment_data ?? [];
@endphp
<div class="container">
    <div class="header">
        <div class="header-nav">
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
            <div class="badge">KELOLA AKUN &amp; DATA DIRI</div>
            @include('layouts.partials.sa-menu-toggle')
        </div>
        <h1 class="title">Kelola Akun Bruder</h1>
        <p class="subtitle">Perbarui kredensial, peran, dan data diri lengkap {{ $user->name }}.</p>
    </div>
    <div class="body">
        @if ($errors->any())
            <div style="background:#FEF3F2; border:1px solid #FECDCA; border-radius:12px; padding:12px 14px; color:#B42318; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.users.account.update', $user) }}" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:14px;">
            @csrf
            <input type="hidden" name="update_biodata" value="1">

            <!-- TAB JUMP NAVIGATION UNTUK MOBILE -->
            <div style="position:sticky; top:12px; z-index:40; background:rgba(255,255,255,0.92); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-radius:14px; padding:6px; box-shadow:0 4px 12px rgba(0,62,151,0.08); border:1px solid rgba(20,85,192,0.15); display:flex; gap:6px; overflow-x:auto;">
                <button type="button" onclick="document.getElementById('sec-sa-akun').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#EFF8FF; color:#1455C0; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">1. Akun</button>
                <button type="button" onclick="document.getElementById('sec-sa-pokok').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#F8F9FC; color:#344054; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">2. Pokok</button>
                <button type="button" onclick="document.getElementById('sec-sa-keluarga').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#F8F9FC; color:#344054; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">3. Keluarga</button>
                <button type="button" onclick="document.getElementById('sec-sa-pendidikan').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#F8F9FC; color:#344054; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">4. Pendidikan</button>
                <button type="button" onclick="document.getElementById('sec-sa-formasi').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#F8F9FC; color:#344054; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">5. Formasi</button>
                <button type="button" onclick="document.getElementById('sec-sa-penugasan').scrollIntoView({behavior:'smooth', block:'start'})" style="border:none; background:#F8F9FC; color:#344054; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:800; white-space:nowrap; cursor:pointer;">6. Penugasan</button>
            </div>

            <!-- SEKSI 1: IDENTITAS & AKSES AKUN -->
            <div class="form-section" id="sec-sa-akun">
                <div class="section-label">1. Identitas, Foto &amp; Akses Akun</div>

                <!-- Avatar & Foto Profil -->
                @php
                    $words = explode(' ', trim($user->name));
                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                    $hasPhoto = $user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo);
                @endphp
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; margin-bottom:12px;">
                    <div style="width:84px; height:84px; border-radius:50%; overflow:hidden; border:3px solid #fff; box-shadow:0 4px 14px rgba(20,85,192,0.2); margin-bottom:8px; background:#1455C0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:26px; font-weight:800;">
                        @if($hasPhoto)
                            <img id="avatarPreviewImg" src="{{ route('superadmin.users.photo', $user) }}" alt="Foto {{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <img id="avatarPreviewImg" src="" alt="Foto" style="width:100%; height:100%; object-fit:cover; display:none;">
                            <span id="avatarInitials">{{ $initials }}</span>
                        @endif
                    </div>
                </div>

                <div class="field">
                    <label>Foto Profil</label>
                    <label for="profile_photo" style="background:rgba(255,255,255,0.7); border:1.5px dashed #B2DDFF; border-radius:12px; padding:14px; text-align:center; cursor:pointer; display:block;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#1455C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:24px; height:24px; margin:0 auto 4px; display:block;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <div style="font-size:13px; font-weight:700; color:#1455C0;">{{ $hasPhoto ? 'Ganti Foto Profil' : 'Pilih Foto Profil Baru' }}</div>
                        <div style="font-size:11px; color:#667085; margin-top:2px;">Format JPG, PNG, atau WEBP. Otomatis dikompresi.</div>
                        <div id="accountPhotoName" style="font-size:12px; font-weight:700; color:#027A48; margin-top:4px;" hidden></div>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" style="display:none;">
                    </label>

                    @if($hasPhoto)
                        <label style="display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#B42318; margin-top:6px; cursor:pointer;">
                            <input type="checkbox" name="remove_photo" value="1">
                            Hapus foto saat ini (kembalikan ke inisial)
                        </label>
                    @endif
                </div>

                <div class="field">
                    <label for="username">Username Akses (Permanen)</label>
                    <input type="text" id="username" value="{{ $user->username }}" readonly tabindex="-1" aria-readonly="true">
                    <p class="hint">Username adalah kunci login unik dan tidak dapat diubah.</p>
                </div>

                <div class="field">
                    <label for="role">Peran (Role) Akses</label>
                    <select id="role" name="role" required>
                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Bruder Muda (User)</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (TPPBM)</option>
                        <option value="admin_ketua_tppbm" {{ old('role', $user->role) === 'admin_ketua_tppbm' ? 'selected' : '' }}>Admin Ketua TPPBM</option>
                        @if($user->role === 'super_admin')
                            <option value="super_admin" selected>Super Admin</option>
                        @endif
                    </select>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Kosongkan jika akun tidak memakai email" autocomplete="off">
                </div>

                <div class="field">
                    <label for="password">Kata Sandi Baru (Opsional / Reset)</label>
                    <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" autocomplete="new-password">
                </div>

                <div class="field">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                </div>
            </div>

            <!-- SEKSI 2: DATA POKOK & KONTAK -->
            <div class="form-section" id="sec-sa-pokok">
                <div class="section-label">2. Data Pokok &amp; Kontak</div>

                <div class="field">
                    <label for="name">Nama Lengkap &amp; Gelar <span style="color:#B42318;">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="field" style="flex:1;">
                        <label for="birth_place">Tempat Lahir</label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" placeholder="Semarang">
                    </div>
                    <div class="field" style="flex:1;">
                        <label for="birth_date">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="field">
                    <label for="phone">No. Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                </div>

                <div class="field">
                    <label for="community_location">Komunitas / Penugasan</label>
                    <input type="text" id="community_location" name="community_location" value="{{ old('community_location', $user->community_location) }}" placeholder="Komunitas Bruderan...">
                </div>

                <div class="field">
                    <label for="bio">Bio / Catatan Pembinaan Awal</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Catatan perihal bruder bersangkutan...">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>

            <!-- SEKSI 3: DATA KELUARGA & NARAHUBUNG -->
            <div class="form-section" id="sec-sa-keluarga">
                <div class="section-label">3. Data Keluarga &amp; Narahubung</div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase;">Data Ayah</div>
                <div class="field">
                    <label>Nama Lengkap Ayah</label>
                    <input type="text" name="family_data[father_name]" value="{{ old('family_data.father_name', $family['father_name'] ?? '') }}" placeholder="Nama ayah kandung">
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:110px;">
                        <label>Tahun Lahir</label>
                        <input type="number" name="family_data[father_birth_year]" value="{{ old('family_data.father_birth_year', $family['father_birth_year'] ?? '') }}" placeholder="1960">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Pekerjaan Ayah</label>
                        <input type="text" name="family_data[father_job]" value="{{ old('family_data.father_job', $family['father_job'] ?? '') }}" placeholder="Pekerjaan ayah">
                    </div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">Data Ibu</div>
                <div class="field">
                    <label>Nama Lengkap Ibu</label>
                    <input type="text" name="family_data[mother_name]" value="{{ old('family_data.mother_name', $family['mother_name'] ?? '') }}" placeholder="Nama ibu kandung">
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:110px;">
                        <label>Tahun Lahir</label>
                        <input type="number" name="family_data[mother_birth_year]" value="{{ old('family_data.mother_birth_year', $family['mother_birth_year'] ?? '') }}" placeholder="1965">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Pekerjaan Ibu</label>
                        <input type="text" name="family_data[mother_job]" value="{{ old('family_data.mother_job', $family['mother_job'] ?? '') }}" placeholder="Pekerjaan ibu">
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <div class="field" style="flex:1;">
                        <label>Jumlah Kakak</label>
                        <input type="number" name="family_data[older_siblings_count]" value="{{ old('family_data.older_siblings_count', $family['older_siblings_count'] ?? '') }}" placeholder="0">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Jumlah Adik</label>
                        <input type="number" name="family_data[younger_siblings_count]" value="{{ old('family_data.younger_siblings_count', $family['younger_siblings_count'] ?? '') }}" placeholder="0">
                    </div>
                </div>

                <div class="field">
                    <label>Asal Paroki</label>
                    <input type="text" name="family_data[parish_origin]" value="{{ old('family_data.parish_origin', $family['parish_origin'] ?? '') }}" placeholder="Contoh: Paroki St. Antonius Purbayan Surakarta">
                </div>

                <div class="field">
                    <label>Alamat Rumah Orang Tua</label>
                    <textarea name="family_data[home_address]" rows="2" placeholder="Alamat lengkap keluarga...">{{ old('family_data.home_address', $family['home_address'] ?? '') }}</textarea>
                </div>

                <div style="font-size:12px; font-weight:800; color:#B57900; text-transform:uppercase; margin-top:10px;">Narahubung Darurat</div>
                <div class="field">
                    <label>Nama Narahubung</label>
                    <input type="text" name="family_data[emergency_name]" value="{{ old('family_data.emergency_name', $family['emergency_name'] ?? '') }}" placeholder="Nama kerabat/saudara">
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="flex:1;">
                        <label>Status Hubungan</label>
                        <input type="text" name="family_data[emergency_relation]" value="{{ old('family_data.emergency_relation', $family['emergency_relation'] ?? '') }}" placeholder="Kakak Kandung / Paman">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>No. HP/WA</label>
                        <input type="text" name="family_data[emergency_phone]" value="{{ old('family_data.emergency_phone', $family['emergency_phone'] ?? '') }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div class="field">
                    <label>Alamat Narahubung</label>
                    <input type="text" name="family_data[emergency_address]" value="{{ old('family_data.emergency_address', $family['emergency_address'] ?? '') }}" placeholder="Kota / Alamat narahubung">
                </div>
            </div>

            <!-- SEKSI 4: RIWAYAT PENDIDIKAN FORMAL -->
            <div class="form-section" id="sec-sa-pendidikan">
                <div class="section-label">4. Riwayat Pendidikan Formal</div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase;">SD</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="flex:1;">
                        <label>Nama Sekolah SD</label>
                        <input type="text" name="education_data[sd_name]" value="{{ old('education_data.sd_name', $education['sd_name'] ?? '') }}" placeholder="SD Kanisius...">
                    </div>
                    <div class="field" style="width:110px;">
                        <label>Tahun Lulus</label>
                        <input type="number" name="education_data[sd_year]" value="{{ old('education_data.sd_year', $education['sd_year'] ?? '') }}" placeholder="2010">
                    </div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">SMP</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="flex:1;">
                        <label>Nama Sekolah SMP</label>
                        <input type="text" name="education_data[smp_name]" value="{{ old('education_data.smp_name', $education['smp_name'] ?? '') }}" placeholder="SMP Pangudi Luhur...">
                    </div>
                    <div class="field" style="width:110px;">
                        <label>Tahun Lulus</label>
                        <input type="number" name="education_data[smp_year]" value="{{ old('education_data.smp_year', $education['smp_year'] ?? '') }}" placeholder="2013">
                    </div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">SMA / SMK</div>
                <div class="field">
                    <label>Nama Sekolah SMA/SMK</label>
                    <input type="text" name="education_data[sma_name]" value="{{ old('education_data.sma_name', $education['sma_name'] ?? '') }}" placeholder="SMA Pangudi Luhur...">
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="flex:1;">
                        <label>Jurusan</label>
                        <input type="text" name="education_data[sma_major]" value="{{ old('education_data.sma_major', $education['sma_major'] ?? '') }}" placeholder="IPA / IPS / Mesin">
                    </div>
                    <div class="field" style="width:110px;">
                        <label>Tahun Lulus</label>
                        <input type="number" name="education_data[sma_year]" value="{{ old('education_data.sma_year', $education['sma_year'] ?? '') }}" placeholder="2016">
                    </div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">Sarjana (S1) - Opsional</div>
                <div class="field">
                    <label>Nama Kampus S1</label>
                    <input type="text" name="education_data[s1_name]" value="{{ old('education_data.s1_name', $education['s1_name'] ?? '') }}" placeholder="Universitas...">
                </div>
                <div class="field">
                    <label>Program Studi</label>
                    <input type="text" name="education_data[s1_major]" value="{{ old('education_data.s1_major', $education['s1_major'] ?? '') }}" placeholder="Ilmu Filsafat / Pendidikan">
                </div>
                <div style="display:flex; gap:8px;">
                    <div class="field" style="flex:1;">
                        <label>Th. Masuk</label>
                        <input type="number" name="education_data[s1_in]" value="{{ old('education_data.s1_in', $education['s1_in'] ?? '') }}">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Target</label>
                        <input type="number" name="education_data[s1_target]" value="{{ old('education_data.s1_target', $education['s1_target'] ?? '') }}">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Lulus Th.</label>
                        <input type="number" name="education_data[s1_out]" value="{{ old('education_data.s1_out', $education['s1_out'] ?? '') }}">
                    </div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">Magister (S2) - Opsional</div>
                <div class="field">
                    <label>Nama Kampus S2</label>
                    <input type="text" name="education_data[s2_name]" value="{{ old('education_data.s2_name', $education['s2_name'] ?? '') }}" placeholder="Universitas...">
                </div>
                <div class="field">
                    <label>Program Studi</label>
                    <input type="text" name="education_data[s2_major]" value="{{ old('education_data.s2_major', $education['s2_major'] ?? '') }}">
                </div>
                <div style="display:flex; gap:8px;">
                    <div class="field" style="flex:1;">
                        <label>Th. Masuk</label>
                        <input type="number" name="education_data[s2_in]" value="{{ old('education_data.s2_in', $education['s2_in'] ?? '') }}">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Target</label>
                        <input type="number" name="education_data[s2_target]" value="{{ old('education_data.s2_target', $education['s2_target'] ?? '') }}">
                    </div>
                    <div class="field" style="flex:1;">
                        <label>Lulus Th.</label>
                        <input type="number" name="education_data[s2_out]" value="{{ old('education_data.s2_out', $education['s2_out'] ?? '') }}">
                    </div>
                </div>
            </div>

            <!-- SEKSI 5: RIWAYAT FORMASI BRUDER FIC -->
            <div class="form-section" id="sec-sa-formasi">
                <div class="section-label">5. Riwayat Formasi Bruder FIC</div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase;">1. Postulat</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:100px;"><label>Th. Masuk</label><input type="number" name="formation_data[postulat_in]" value="{{ old('formation_data.postulat_in', $formation['postulat_in'] ?? '') }}"></div>
                    <div class="field" style="width:100px;"><label>Jml Angkatan</label><input type="number" name="formation_data[postulat_count]" value="{{ old('formation_data.postulat_count', $formation['postulat_count'] ?? '') }}"></div>
                    <div class="field" style="flex:1;"><label>Nama Magister</label><input type="text" name="formation_data[postulat_magister]" value="{{ old('formation_data.postulat_magister', $formation['postulat_magister'] ?? '') }}"></div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">2. Novisiat Kanonik</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:100px;"><label>Th. Masuk</label><input type="number" name="formation_data[kanonik_in]" value="{{ old('formation_data.kanonik_in', $formation['kanonik_in'] ?? '') }}"></div>
                    <div class="field" style="width:100px;"><label>Jml Angkatan</label><input type="number" name="formation_data[kanonik_count]" value="{{ old('formation_data.kanonik_count', $formation['kanonik_count'] ?? '') }}"></div>
                    <div class="field" style="flex:1;"><label>Nama Magister</label><input type="text" name="formation_data[kanonik_magister]" value="{{ old('formation_data.kanonik_magister', $formation['kanonik_magister'] ?? '') }}"></div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">3. Novisiat Apostolik</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:100px;"><label>Th. Masuk</label><input type="number" name="formation_data[apostolik_in]" value="{{ old('formation_data.apostolik_in', $formation['apostolik_in'] ?? '') }}"></div>
                    <div class="field" style="width:100px;"><label>Jml Angkatan</label><input type="number" name="formation_data[apostolik_count]" value="{{ old('formation_data.apostolik_count', $formation['apostolik_count'] ?? '') }}"></div>
                    <div class="field" style="flex:1;"><label>Nama Magister</label><input type="text" name="formation_data[apostolik_magister]" value="{{ old('formation_data.apostolik_magister', $formation['apostolik_magister'] ?? '') }}"></div>
                </div>

                <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-top:8px;">4. Prasetia Pertama</div>
                <div style="display:flex; gap:10px;">
                    <div class="field" style="width:100px;"><label>Tahun Prasetia</label><input type="number" name="formation_data[prasetia_1_year]" value="{{ old('formation_data.prasetia_1_year', $formation['prasetia_1_year'] ?? '') }}"></div>
                    <div class="field" style="width:100px;"><label>Jml Angkatan</label><input type="number" name="formation_data[prasetia_1_count]" value="{{ old('formation_data.prasetia_1_count', $formation['prasetia_1_count'] ?? '') }}"></div>
                    <div class="field" style="flex:1;"><label>Nama Provinsial</label><input type="text" name="formation_data[prasetia_1_provincial]" value="{{ old('formation_data.prasetia_1_provincial', $formation['prasetia_1_provincial'] ?? '') }}"></div>
                </div>

                @for($p = 1; $p <= 6; $p++)
                    <div style="font-size:12px; font-weight:800; color:#667085; text-transform:uppercase; margin-top:8px;">Pembaruan Ke-{{ $p }} (Opsional)</div>
                    <div style="display:flex; gap:10px;">
                        <div class="field" style="width:100px;"><label>Tahun</label><input type="number" name="formation_data[pembaruan][{{ $p }}][year]" value="{{ old("formation_data.pembaruan.{$p}.year", $formation['pembaruan'][$p]['year'] ?? '') }}"></div>
                        <div class="field" style="width:100px;"><label>Angkatan</label><input type="number" name="formation_data[pembaruan][{{ $p }}][count]" value="{{ old("formation_data.pembaruan.{$p}.count", $formation['pembaruan'][$p]['count'] ?? '') }}"></div>
                        <div class="field" style="flex:1;"><label>Nama Provinsial</label><input type="text" name="formation_data[pembaruan][{{ $p }}][provincial]" value="{{ old("formation_data.pembaruan.{$p}.provincial", $formation['pembaruan'][$p]['provincial'] ?? '') }}"></div>
                    </div>
                @endfor
            </div>

            <!-- SEKSI 6: RIWAYAT PENUGASAN KOMUNITAS -->
            <div class="form-section" id="sec-sa-penugasan">
                <div class="section-label">6. Riwayat Komunitas &amp; Penugasan</div>

                @for($t = 1; $t <= 6; $t++)
                    <div style="font-size:12px; font-weight:800; color: {{ $t === 1 ? '#027A48' : '#667085' }}; text-transform:uppercase; margin-top: {{ $t > 1 ? '12px' : '0' }};">
                        Tugas Ke-{{ $t }} {{ $t === 1 ? '(Tugas Pertama)' : '(Opsional)' }}
                    </div>
                    <div class="field">
                        <label>Nama Komunitas</label>
                        <input type="text" name="assignment_data[{{ $t }}][community]" value="{{ old("assignment_data.{$t}.community", $assignment[$t]['community'] ?? '') }}" placeholder="Komunitas Bruderan...">
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div class="field" style="flex:1;"><label>Th. Tinggal</label><input type="number" name="assignment_data[{{ $t }}][start_year]" value="{{ old("assignment_data.{$t}.start_year", $assignment[$t]['start_year'] ?? '') }}"></div>
                        <div class="field" style="flex:1;"><label>Th. Pindah</label><input type="number" name="assignment_data[{{ $t }}][end_year]" value="{{ old("assignment_data.{$t}.end_year", $assignment[$t]['end_year'] ?? '') }}"></div>
                    </div>
                    <div class="field">
                        <label>Tugas Pokok</label>
                        <input type="text" name="assignment_data[{{ $t }}][duty]" value="{{ old("assignment_data.{$t}.duty", $assignment[$t]['duty'] ?? '') }}" placeholder="Guru / Pamong / Bendahara">
                    </div>
                @endfor
            </div>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn-submit">SIMPAN SELURUH PERUBAHAN</button>
                <a href="{{ route('superadmin.users.index') }}" class="back-link">← Batal &amp; Kembali ke Daftar</a>
            </div>
        </form>
    </div>
    @include('layouts.partials.sa-menu')
</div>

<script>
    (function() {
        const input = document.getElementById('profile_photo');
        if (!input) return;

        input.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const nameEl = document.getElementById('accountPhotoName');
            const previewImg = document.getElementById('avatarPreviewImg');
            const initialsSpan = document.getElementById('avatarInitials');

            // Baca & kompres via Canvas bila perlu
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = new Image();
                img.onload = function() {
                    const MAX = 1200;
                    let w = img.width;
                    let h = img.height;
                    if (w > h && w > MAX) {
                        h = Math.round((h * MAX) / w);
                        w = MAX;
                    } else if (h > MAX) {
                        w = Math.round((w * MAX) / h);
                        h = MAX;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                    canvas.toBlob(function(blob) {
                        if (blob && window.DataTransfer) {
                            const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            const dt = new DataTransfer();
                            dt.items.add(newFile);
                            input.files = dt.files;
                        }

                        if (previewImg) {
                            previewImg.src = canvas.toDataURL('image/jpeg', 0.85);
                            previewImg.style.display = 'block';
                            if (initialsSpan) initialsSpan.style.display = 'none';
                        }
                        if (nameEl) {
                            nameEl.hidden = false;
                            nameEl.textContent = 'Foto siap: ' + file.name + ' (' + Math.round((blob ? blob.size : file.size) / 1024) + ' KB)';
                        }
                    }, 'image/jpeg', 0.85);
                };
                img.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
</body>
</html>
