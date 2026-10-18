<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Tambah Bruder Baru - PWA BRUDER FIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/fic-pwa.css">
    <style>
        body { background:#EEF2F6; font-family:'Plus Jakarta Sans','Inter',sans-serif; margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; }
        .container { width:100%; max-width:480px; background:#F6F8FC; min-height:100vh; display:flex; flex-direction:column; box-shadow:0 0 30px rgba(0,0,0,0.08); padding-bottom:72px; box-sizing:border-box; }
        .header { background:linear-gradient(135deg,#003E97 0%,#1455C0 100%); color:#fff; padding:20px; border-bottom-left-radius:20px; border-bottom-right-radius:20px; box-shadow:0 4px 16px rgba(0,62,151,0.15); }
        .header-nav { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .btn-header-back { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0; line-height:0; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.3); border-radius:10px; color:#fff; text-decoration:none; }
        .btn-header-back svg { display:block; }
        .badge { display:inline-flex; gap:6px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; text-transform:uppercase; }
        .title { font-size:20px; font-weight:800; margin:0 0 4px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 96px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .form-section { background:#fff; border-radius:16px; border:1px solid #E4E7EC; padding:18px; box-shadow:0 2px 8px rgba(16,24,40,0.04); display:flex; flex-direction:column; gap:14px; }
        .section-label { font-size:12px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin-bottom:4px; }
        .field { display:flex; flex-direction:column; gap:6px; }
        .field label { font-size:12px; font-weight:700; color:#475467; text-transform:uppercase; letter-spacing:0.4px; }
        .field input, .field textarea, .field select { width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid #D0D5DD; font-size:14px; background:#fff; font-family:inherit; color:#101828; }
        .field input:focus, .field textarea:focus { border-color:#1455C0; outline:none; box-shadow:0 0 0 3px rgba(20,85,192,0.12); }
        .btn-submit { border:none; border-radius:14px; padding:15px; font-weight:800; font-size:15px; background:linear-gradient(135deg, #1455C0 0%, #003E97 100%); color:#fff; cursor:pointer; box-shadow:0 4px 12px rgba(20,85,192,0.25); width:100%; }
        .back-link { display:block; text-align:center; margin-top:8px; color:#1455C0; text-decoration:none; font-weight:700; font-size:13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-nav">
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
            <div class="badge"><span>👤</span> PEMBUATAN AKUN</div>
            @include('layouts.partials.sa-menu-toggle')
        </div>
        <h1 class="title">Tambah Bruder Baru</h1>
        <p class="subtitle">Isi kredensial akun &amp; data diri Bruder Muda.</p>
    </div>
    <div class="body">
        @if ($errors->any())
            <div style="background:#FEF3F2; border:1px solid #FECDCA; border-radius:12px; padding:12px 14px; color:#B42318; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.users.store') }}">
            @csrf

            <!-- Form Card 1: Akun & Kredensial -->
            <div class="form-section">
                <div class="section-label">1. Kredensial Akun Login</div>

                <div class="field">
                    <label for="name">Nama Lengkap &amp; Gelar</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Br. Fransiskus Xaverius FIC" required>
                </div>

                <div class="field">
                    <label for="username">Username Akses</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: bruder_fx" required>
                </div>

                <div class="field">
                    <label for="email">Email (Opsional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="bruder.fx@fic.org">
                </div>

                <div class="field">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
            </div>

            <!-- Form Card 2: Data Diri & Keanggotaan Bruder -->
            <div class="form-section" style="margin-top: 14px;">
                <div class="section-label">2. Data Diri &amp; Keanggotaan Bruder</div>

                <div class="field">
                    <label for="birth_date">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                </div>

                <div class="field">
                    <label for="parents_name">Nama Orang Tua (Ayah / Ibu)</label>
                    <input type="text" id="parents_name" name="parents_name" value="{{ old('parents_name') }}" placeholder="Contoh: Bpk. Yohanes & Ibu Maria">
                </div>

                <div class="field">
                    <label for="last_education">Pendidikan Terakhir</label>
                    <input type="text" id="last_education" name="last_education" value="{{ old('last_education') }}" placeholder="Contoh: S1 Filsafat / S1 Pendidikan">
                </div>

                <div class="field">
                    <label for="entry_year">Tahun Masuk Bruder FIC</label>
                    <input type="number" id="entry_year" name="entry_year" value="{{ old('entry_year') }}" placeholder="Contoh: 2020" min="1950" max="{{ date('Y') }}">
                </div>

                <div class="field">
                    <label for="community_location">Lokasi Komunitas / Penugasan</label>
                    <input type="text" id="community_location" name="community_location" value="{{ old('community_location') }}" placeholder="Contoh: Komunitas Bruderan Deltamas">
                </div>

                <div class="field">
                    <label for="phone">No. Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890">
                </div>

                <div class="field">
                    <label for="bio">Bio / Catatan Pembinaan</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Catatan singkat perihal bruder muda...">{{ old('bio') }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div style="margin-top: 16px;">
                <button type="submit" class="btn-submit">SIMPAN &amp; BUAT AKUN</button>
                <a href="{{ route('superadmin.users.index') }}" class="back-link">← Batal &amp; Kembali ke Daftar</a>
            </div>
        </form>
    </div>
        @include('layouts.partials.sa-menu')
</div>
</body>
</html>
