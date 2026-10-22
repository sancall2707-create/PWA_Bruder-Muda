@extends('layouts.user-app')

@php
    $backUrl = route('profil');
    $pageTitle = 'Edit Profil Lengkap';
    $title = 'Edit Profil Lengkap';
    $hideBottomNav = true;
    $family = $user->family_data ?? [];
    $education = $user->education_data ?? [];
    $formation = $user->formation_data ?? [];
    $assignment = $user->assignment_data ?? [];
@endphp

@section('content')
<div class="card" style="margin-bottom: 14px;">
    <div style="margin-bottom: 10px;">
        <h2 style="font-size: 17px; font-weight: 800; color: var(--fic-text-primary); margin:0;">Kelengkapan Data Profil Bruder</h2>
        <p style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 4px;">
            Lengkapi data diri, keluarga, narahubung, riwayat pendidikan, formasi FIC, dan riwayat penugasan Anda.
        </p>
    </div>
</div>

@if ($errors->any())
    <div style="background-color: #FEF3F2; border: 1px solid #FECDCA; border-radius: 12px; padding: 12px 14px; color: #B42318; font-size: 13px; margin-bottom: 14px;">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:14px;">
    @csrf

    <!-- SECTION 1: DATA AKUN & PRIBADI -->
    <div class="card" style="padding:20px 16px;">
        <h3 style="font-size:14px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin:0 0 16px; text-align:center;">
            1. Data Pokok &amp; Kontak
        </h3>

        <!-- Avatar & Profil Center -->
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; margin-bottom: 16px;">
            <div style="display: flex; justify-content: center; margin-bottom: 10px;">
                @if ($photoUrl)
                    <img id="userAvatarPreviewImg" src="{{ $photoUrl }}" alt="Foto profil saat ini" width="88" height="88" style="width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 4px 14px rgba(20, 85, 192, 0.25); display: block; margin: 0 auto;">
                @else
                    @php
                        $words = explode(' ', trim($user->name));
                        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                    @endphp
                    <img id="userAvatarPreviewImg" src="" alt="Foto" width="88" height="88" style="width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 3px solid #ffffff; box-shadow: 0 4px 14px rgba(20, 85, 192, 0.25); display: none; margin: 0 auto;">
                    <div id="userAvatarInitials" style="width: 88px; height: 88px; border-radius: 50%; background: linear-gradient(135deg, var(--fic-primary) 0%, var(--fic-dark-blue) 100%); color: #ffffff; font-size: 28px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 4px 14px rgba(20, 85, 192, 0.25); border: 3px solid #ffffff;">
                        {{ $initials }}
                    </div>
                @endif
            </div>
            <div style="font-size: 15px; font-weight: 800; color: var(--fic-text-primary);">{{ $user->name }}</div>
            <div style="font-size: 12px; color: var(--fic-text-secondary); margin-top: 2px;">{{ '@' . $user->username }}</div>
        </div>

        <div class="form-group">
            <label for="profile_photo" class="file-upload-box">
                <svg class="file-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <div class="file-upload-text">{{ $photoUrl ? 'Ganti Foto Profil' : 'Tambahkan Foto Profil' }}</div>
                <div class="file-upload-subtext">JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.</div>
                <div class="file-upload-filename" id="profilePhotoName" hidden></div>
                <input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="file-upload-input">
            </label>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" required maxlength="255">
        </div>

        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" value="{{ $user->username }}" class="form-input" disabled>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email (Opsional)</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nama@fic.org" class="form-input" maxlength="255">
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">No. Telepon / WhatsApp <span class="required">*</span></label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" class="form-input">
        </div>

        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;">
                <label for="birth_place" class="form-label">Tempat Lahir <span class="required">*</span></label>
                <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}" placeholder="Semarang" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label for="birth_date" class="form-label">Tanggal Lahir <span class="required">*</span></label>
                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}" class="form-input">
            </div>
        </div>

        <div class="form-group">
            <label for="community_location" class="form-label">Komunitas Saat Ini</label>
            <input type="text" id="community_location" name="community_location" value="{{ old('community_location', $user->community_location) }}" placeholder="Contoh: Komunitas Bruderan Deltamas" class="form-input">
        </div>
    </div>

    <!-- SECTION 2: DATA KELUARGA & NARAHUBUNG -->
    <div class="card" style="padding:16px;">
        <h3 style="font-size:14px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin:0 0 14px; text-align:center;">
            2. Data Keluarga &amp; Narahubung
        </h3>

        <!-- Ayah -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-bottom:8px;">Data Ayah</div>
        <div class="form-group">
            <label class="form-label">Nama Lengkap Ayah</label>
            <input type="text" name="family_data[father_name]" value="{{ old('family_data.father_name', $family['father_name'] ?? '') }}" placeholder="Nama ayah kandung" class="form-input">
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:120px;">
                <label class="form-label">Tahun Lahir</label>
                <input type="number" name="family_data[father_birth_year]" value="{{ old('family_data.father_birth_year', $family['father_birth_year'] ?? '') }}" placeholder="1960" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Pekerjaan Ayah</label>
                <input type="text" name="family_data[father_job]" value="{{ old('family_data.father_job', $family['father_job'] ?? '') }}" placeholder="Pensiunan / Wiraswasta" class="form-input">
            </div>
        </div>

        <!-- Ibu -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:14px 0 8px;">Data Ibu</div>
        <div class="form-group">
            <label class="form-label">Nama Lengkap Ibu</label>
            <input type="text" name="family_data[mother_name]" value="{{ old('family_data.mother_name', $family['mother_name'] ?? '') }}" placeholder="Nama ibu kandung" class="form-input">
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:120px;">
                <label class="form-label">Tahun Lahir</label>
                <input type="number" name="family_data[mother_birth_year]" value="{{ old('family_data.mother_birth_year', $family['mother_birth_year'] ?? '') }}" placeholder="1965" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Pekerjaan Ibu</label>
                <input type="text" name="family_data[mother_job]" value="{{ old('family_data.mother_job', $family['mother_job'] ?? '') }}" placeholder="Ibu Rumah Tangga" class="form-input">
            </div>
        </div>

        <!-- Saudara & Paroki -->
        <div style="display:flex; gap:10px; margin-top:6px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Jumlah Kakak</label>
                <input type="number" name="family_data[older_siblings_count]" value="{{ old('family_data.older_siblings_count', $family['older_siblings_count'] ?? '') }}" placeholder="0" class="form-input" min="0">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Jumlah Adik</label>
                <input type="number" name="family_data[younger_siblings_count]" value="{{ old('family_data.younger_siblings_count', $family['younger_siblings_count'] ?? '') }}" placeholder="0" class="form-input" min="0">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Asal Paroki</label>
            <input type="text" name="family_data[parish_origin]" value="{{ old('family_data.parish_origin', $family['parish_origin'] ?? '') }}" placeholder="Contoh: Paroki St. Antonius Purbayan Surakarta" class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Rumah Orang Tua</label>
            <textarea name="family_data[home_address]" rows="2" placeholder="Alamat lengkap keluarga..." class="form-input">{{ old('family_data.home_address', $family['home_address'] ?? '') }}</textarea>
        </div>

        <!-- Narahubung Darurat -->
        <div style="font-size:12px; font-weight:800; color:#B57900; text-transform:uppercase; margin:16px 0 8px;">Narahubung Darurat</div>
        <div class="form-group">
            <label class="form-label">Nama Narahubung</label>
            <input type="text" name="family_data[emergency_name]" value="{{ old('family_data.emergency_name', $family['emergency_name'] ?? '') }}" placeholder="Nama kerabat/saudara" class="form-input">
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Status Keluarga</label>
                <input type="text" name="family_data[emergency_relation]" value="{{ old('family_data.emergency_relation', $family['emergency_relation'] ?? '') }}" placeholder="Kakak Kandung / Paman" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">No. HP/WA</label>
                <input type="text" name="family_data[emergency_phone]" value="{{ old('family_data.emergency_phone', $family['emergency_phone'] ?? '') }}" placeholder="08xxxxxxxxxx" class="form-input">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Narahubung</label>
            <input type="text" name="family_data[emergency_address]" value="{{ old('family_data.emergency_address', $family['emergency_address'] ?? '') }}" placeholder="Kota / Alamat narahubung" class="form-input">
        </div>
    </div>

    <!-- SECTION 3: RIWAYAT PENDIDIKAN FORMAL -->
    <div class="card" style="padding:16px;">
        <h3 style="font-size:14px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin:0 0 14px; text-align:center;">
            3. Riwayat Pendidikan Formal
        </h3>

        <!-- SD -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-bottom:6px;">Pendidikan SD</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Sekolah SD</label>
                <input type="text" name="education_data[sd_name]" value="{{ old('education_data.sd_name', $education['sd_name'] ?? '') }}" placeholder="SD Kanisius..." class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Lulus Tahun</label>
                <input type="number" name="education_data[sd_year]" value="{{ old('education_data.sd_year', $education['sd_year'] ?? '') }}" placeholder="2010" class="form-input">
            </div>
        </div>

        <!-- SMP -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:10px 0 6px;">Pendidikan SMP</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Sekolah SMP</label>
                <input type="text" name="education_data[smp_name]" value="{{ old('education_data.smp_name', $education['smp_name'] ?? '') }}" placeholder="SMP Pangudi Luhur..." class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Lulus Tahun</label>
                <input type="number" name="education_data[smp_year]" value="{{ old('education_data.smp_year', $education['smp_year'] ?? '') }}" placeholder="2013" class="form-input">
            </div>
        </div>

        <!-- SMA/SMK -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:10px 0 6px;">Pendidikan SMA / SMK</div>
        <div class="form-group">
            <label class="form-label">Nama Sekolah SMA/SMK</label>
            <input type="text" name="education_data[sma_name]" value="{{ old('education_data.sma_name', $education['sma_name'] ?? '') }}" placeholder="SMA Pangudi Luhur..." class="form-input">
        </div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Jurusan</label>
                <input type="text" name="education_data[sma_major]" value="{{ old('education_data.sma_major', $education['sma_major'] ?? '') }}" placeholder="IPA / IPS / Mesin" class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Lulus Tahun</label>
                <input type="number" name="education_data[sma_year]" value="{{ old('education_data.sma_year', $education['sma_year'] ?? '') }}" placeholder="2016" class="form-input">
            </div>
        </div>

        <!-- S1 (Opsional) -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:12px 0 6px;">Pendidikan Sarjana (S1) - Opsional</div>
        <div class="form-group">
            <label class="form-label">Nama Kampus / Universitas</label>
            <input type="text" name="education_data[s1_name]" value="{{ old('education_data.s1_name', $education['s1_name'] ?? '') }}" placeholder="Universitas Sanata Dharma..." class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Program Studi</label>
            <input type="text" name="education_data[s1_major]" value="{{ old('education_data.s1_major', $education['s1_major'] ?? '') }}" placeholder="Ilmu Filsafat / Pendidikan" class="form-input">
        </div>
        <div style="display:flex; gap:8px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Th. Masuk</label>
                <input type="number" name="education_data[s1_in]" value="{{ old('education_data.s1_in', $education['s1_in'] ?? '') }}" placeholder="2018" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Target Lulus</label>
                <input type="number" name="education_data[s1_target]" value="{{ old('education_data.s1_target', $education['s1_target'] ?? '') }}" placeholder="2022" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Lulus Tahun</label>
                <input type="number" name="education_data[s1_out]" value="{{ old('education_data.s1_out', $education['s1_out'] ?? '') }}" placeholder="2022" class="form-input">
            </div>
        </div>

        <!-- S2 (Opsional) -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:12px 0 6px;">Pendidikan Magister (S2) - Opsional</div>
        <div class="form-group">
            <label class="form-label">Nama Kampus S2</label>
            <input type="text" name="education_data[s2_name]" value="{{ old('education_data.s2_name', $education['s2_name'] ?? '') }}" placeholder="Nama universitas..." class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">Program Studi S2</label>
            <input type="text" name="education_data[s2_major]" value="{{ old('education_data.s2_major', $education['s2_major'] ?? '') }}" placeholder="Magister Manajemen..." class="form-input">
        </div>
        <div style="display:flex; gap:8px;">
            <div class="form-group" style="flex:1;">
                <label class="form-label">Th. Masuk</label>
                <input type="number" name="education_data[s2_in]" value="{{ old('education_data.s2_in', $education['s2_in'] ?? '') }}" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Target</label>
                <input type="number" name="education_data[s2_target]" value="{{ old('education_data.s2_target', $education['s2_target'] ?? '') }}" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Lulus Th.</label>
                <input type="number" name="education_data[s2_out]" value="{{ old('education_data.s2_out', $education['s2_out'] ?? '') }}" class="form-input">
            </div>
        </div>
    </div>

    <!-- SECTION 4: RIWAYAT FORMASI BRUDER FIC -->
    <div class="card" style="padding:16px;">
        <h3 style="font-size:14px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin:0 0 14px; text-align:center;">
            4. Riwayat Formasi Bruder FIC
        </h3>

        <!-- Postulat -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin-bottom:6px;">1. Postulat</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:110px;">
                <label class="form-label">Th. Masuk</label>
                <input type="number" name="formation_data[postulat_in]" value="{{ old('formation_data.postulat_in', $formation['postulat_in'] ?? '') }}" placeholder="2016" class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Jml Angkatan</label>
                <input type="number" name="formation_data[postulat_count]" value="{{ old('formation_data.postulat_count', $formation['postulat_count'] ?? '') }}" placeholder="8" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Magister</label>
                <input type="text" name="formation_data[postulat_magister]" value="{{ old('formation_data.postulat_magister', $formation['postulat_magister'] ?? '') }}" placeholder="Br. ..." class="form-input">
            </div>
        </div>

        <!-- Novisiat Kanonik -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:10px 0 6px;">2. Novisiat Kanonik</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:110px;">
                <label class="form-label">Th. Masuk</label>
                <input type="number" name="formation_data[kanonik_in]" value="{{ old('formation_data.kanonik_in', $formation['kanonik_in'] ?? '') }}" placeholder="2017" class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Jml Angkatan</label>
                <input type="number" name="formation_data[kanonik_count]" value="{{ old('formation_data.kanonik_count', $formation['kanonik_count'] ?? '') }}" placeholder="7" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Magister</label>
                <input type="text" name="formation_data[kanonik_magister]" value="{{ old('formation_data.kanonik_magister', $formation['kanonik_magister'] ?? '') }}" placeholder="Br. ..." class="form-input">
            </div>
        </div>

        <!-- Novisiat Apostolik -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:10px 0 6px;">3. Novisiat Apostolik</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:110px;">
                <label class="form-label">Th. Masuk</label>
                <input type="number" name="formation_data[apostolik_in]" value="{{ old('formation_data.apostolik_in', $formation['apostolik_in'] ?? '') }}" placeholder="2018" class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Jml Angkatan</label>
                <input type="number" name="formation_data[apostolik_count]" value="{{ old('formation_data.apostolik_count', $formation['apostolik_count'] ?? '') }}" placeholder="7" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Magister</label>
                <input type="text" name="formation_data[apostolik_magister]" value="{{ old('formation_data.apostolik_magister', $formation['apostolik_magister'] ?? '') }}" placeholder="Br. ..." class="form-input">
            </div>
        </div>

        <!-- Prasetia Pertama -->
        <div style="font-size:12px; font-weight:800; color:#344054; text-transform:uppercase; margin:10px 0 6px;">4. Prasetia Pertama</div>
        <div style="display:flex; gap:10px;">
            <div class="form-group" style="width:110px;">
                <label class="form-label">Tahun Prasetia</label>
                <input type="number" name="formation_data[prasetia_1_year]" value="{{ old('formation_data.prasetia_1_year', $formation['prasetia_1_year'] ?? '') }}" placeholder="2019" class="form-input">
            </div>
            <div class="form-group" style="width:110px;">
                <label class="form-label">Jml Angkatan</label>
                <input type="number" name="formation_data[prasetia_1_count]" value="{{ old('formation_data.prasetia_1_count', $formation['prasetia_1_count'] ?? '') }}" placeholder="6" class="form-input">
            </div>
            <div class="form-group" style="flex:1;">
                <label class="form-label">Nama Provinsial</label>
                <input type="text" name="formation_data[prasetia_1_provincial]" value="{{ old('formation_data.prasetia_1_provincial', $formation['prasetia_1_provincial'] ?? '') }}" placeholder="Br. ..." class="form-input">
            </div>
        </div>

        <!-- Pembaruan Prasetia 1 s.d. 6 -->
        @for($p = 1; $p <= 6; $p++)
            <div style="font-size:12px; font-weight:800; color:#667085; text-transform:uppercase; margin:10px 0 6px;">Pembaruan Ke-{{ $p }} (Opsional)</div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="width:110px;">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="formation_data[pembaruan][{{ $p }}][year]" value="{{ old("formation_data.pembaruan.{$p}.year", $formation['pembaruan'][$p]['year'] ?? '') }}" class="form-input">
                </div>
                <div class="form-group" style="width:110px;">
                    <label class="form-label">Angkatan</label>
                    <input type="number" name="formation_data[pembaruan][{{ $p }}][count]" value="{{ old("formation_data.pembaruan.{$p}.count", $formation['pembaruan'][$p]['count'] ?? '') }}" class="form-input">
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Nama Provinsial</label>
                    <input type="text" name="formation_data[pembaruan][{{ $p }}][provincial]" value="{{ old("formation_data.pembaruan.{$p}.provincial", $formation['pembaruan'][$p]['provincial'] ?? '') }}" placeholder="Br. ..." class="form-input">
                </div>
            </div>
        @endfor
    </div>

    <!-- SECTION 5: RIWAYAT PENUGASAN KOMUNITAS -->
    <div class="card" style="padding:16px;">
        <h3 style="font-size:14px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin:0 0 14px; text-align:center;">
            5. Riwayat Komunitas &amp; Penugasan
        </h3>

        @for($t = 1; $t <= 6; $t++)
            <div style="font-size:12px; font-weight:800; color: {{ $t === 1 ? '#027A48' : '#667085' }}; text-transform:uppercase; margin: {{ $t > 1 ? '14px 0 6px' : '0 0 6px' }};">
                Tugas Ke-{{ $t }} {{ $t === 1 ? '(Tugas Pertama)' : '(Opsional)' }}
            </div>
            <div class="form-group">
                <label class="form-label">Nama Komunitas</label>
                <input type="text" name="assignment_data[{{ $t }}][community]" value="{{ old("assignment_data.{$t}.community", $assignment[$t]['community'] ?? '') }}" placeholder="Komunitas Bruderan..." class="form-input">
            </div>
            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Th. Tinggal</label>
                    <input type="number" name="assignment_data[{{ $t }}][start_year]" value="{{ old("assignment_data.{$t}.start_year", $assignment[$t]['start_year'] ?? '') }}" placeholder="2020" class="form-input">
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Th. Pindah</label>
                    <input type="number" name="assignment_data[{{ $t }}][end_year]" value="{{ old("assignment_data.{$t}.end_year", $assignment[$t]['end_year'] ?? '') }}" placeholder="2022" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tugas Pokok</label>
                <input type="text" name="assignment_data[{{ $t }}][duty]" value="{{ old("assignment_data.{$t}.duty", $assignment[$t]['duty'] ?? '') }}" placeholder="Guru / Pamong / Bendahara" class="form-input">
            </div>
        @endfor
    </div>

    <div style="margin: 12px 0 100px;">
        <button type="submit" class="btn-primary" style="padding:15px; font-size:15px; font-weight:800;">
            SIMPAN SELURUH DATA PROFIL
        </button>
        <a href="{{ route('profil') }}" style="display:block; text-align:center; margin-top:10px; color:#1455C0; font-weight:700; font-size:13px; text-decoration:none;">
            ← Batal &amp; Kembali ke Profil
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function () {
        var photoInput = document.getElementById('profile_photo');
        if (!photoInput) return;

        photoInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            var nameEl = document.getElementById('profilePhotoName');
            var previewImg = document.getElementById('userAvatarPreviewImg');
            var initialsDiv = document.getElementById('userAvatarInitials');

            if (!file) {
                if (nameEl) { nameEl.hidden = true; nameEl.textContent = ''; }
                return;
            }

            var reader = new FileReader();
            reader.onload = function (evt) {
                var img = new Image();
                img.onload = function () {
                    var MAX = 1200;
                    var w = img.width;
                    var h = img.height;
                    if (w > h && w > MAX) {
                        h = Math.round((h * MAX) / w);
                        w = MAX;
                    } else if (h > MAX) {
                        w = Math.round((w * MAX) / h);
                        h = MAX;
                    }

                    var canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                    canvas.toBlob(function (blob) {
                        if (blob && window.DataTransfer) {
                            var newFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            var dt = new DataTransfer();
                            dt.items.add(newFile);
                            photoInput.files = dt.files;
                        }

                        if (previewImg) {
                            previewImg.src = canvas.toDataURL('image/jpeg', 0.85);
                            previewImg.style.display = 'block';
                            if (initialsDiv) initialsDiv.style.display = 'none';
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
@endpush
