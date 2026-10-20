<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Tambah Catatan Baru - PWA BRUDER FIC</title>
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
        .field { display:flex; flex-direction:column; gap:6px; }
        .field label { font-size:12px; font-weight:700; color:#475467; text-transform:uppercase; letter-spacing:0.4px; }
        .field select, .field textarea { width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.84); font-size:14px; background:rgba(255,255,255,0.72); backdrop-filter:blur(12px); font-family:inherit; color:#101828; }
        .field select:focus, .field textarea:focus { border-color:#027A48; outline:none; box-shadow:0 0 0 3px rgba(2,122,72,0.14); }
        .btn-submit-green { border:none; border-radius:14px; padding:15px; font-weight:800; font-size:15px; background:linear-gradient(135deg, #027A48 0%, #05603A 100%); color:#fff; cursor:pointer; box-shadow:0 4px 12px rgba(2,122,72,0.28); width:100%; }
        .btn-submit-green:hover { background:linear-gradient(135deg, #05603A 0%, #03482B 100%); }
        .back-link { display:block; text-align:center; margin-top:8px; color:#475467; text-decoration:none; font-weight:700; font-size:13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-nav">
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
            <div class="badge">CATATAN PEMBINAAN</div>
            @include('layouts.partials.sa-menu-toggle')
        </div>
        <h1 class="title">Tambah Catatan Baru</h1>
        <p class="subtitle">Untuk {{ $user->name }} ({{ '@'.$user->username }})</p>
    </div>
    <div class="body">
        @if ($errors->any())
            <div style="background:#FEF3F2; border:1px solid #FECDCA; border-radius:12px; padding:12px 14px; color:#B42318; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.users.notes.store', $user) }}">
            @csrf

            <div class="form-section">
                <div class="field">
                    <label for="category">PILIH KATEGORI</label>
                    <select id="category" name="category" required>
                        <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Catatan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="content">URAIAN CATATAN</label>
                    <textarea id="content" name="content" rows="6" placeholder="Tuliskan uraian catatan pembinaan di sini..." required>{{ old('content') }}</textarea>
                </div>
            </div>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn-submit-green">SIMPAN CATATAN</button>
                <a href="{{ route('superadmin.users.index') }}" class="back-link">Batal</a>
            </div>
        </form>
    </div>
    @include('layouts.partials.sa-menu')
</div>
</body>
</html>
