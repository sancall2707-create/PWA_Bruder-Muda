<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Kirim Pesan Khusus - PWA BRUDER FIC</title>
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
        .field input, .field textarea { width:100%; box-sizing:border-box; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.84); font-size:14px; background:rgba(255,255,255,0.72); backdrop-filter:blur(12px); font-family:inherit; color:#101828; }
        .field input:focus, .field textarea:focus { border-color:#B57900; outline:none; box-shadow:0 0 0 3px rgba(181,121,0,0.15); }
        .btn-submit-gold { border:none; border-radius:14px; padding:15px; font-weight:800; font-size:15px; background:linear-gradient(135deg, #B57900 0%, #8D5D00 100%); color:#fff; cursor:pointer; box-shadow:0 4px 12px rgba(181,121,0,0.28); width:100%; }
        .btn-submit-gold:hover { background:linear-gradient(135deg, #8D5D00 0%, #684400 100%); }
        .back-link { display:block; text-align:center; margin-top:8px; color:#475467; text-decoration:none; font-weight:700; font-size:13px; }
        .msg-history-card { background:#fff; border-radius:12px; border:1px solid #E4E7EC; padding:12px 14px; display:flex; flex-direction:column; gap:4px; font-size:13px; }
        .btn-retract { align-self:flex-start; margin-top:6px; border:1px solid #FECDCA; background:#FEF3F2; color:#B42318; border-radius:8px; padding:6px 10px; font-size:11px; font-weight:800; cursor:pointer; font-family:inherit; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-nav">
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
            <div class="badge">PESAN SATU ARAH</div>
            @include('layouts.partials.sa-menu-toggle')
        </div>
        <h1 class="title">Kirim Pesan Khusus</h1>
        <p class="subtitle">Kepada {{ $user->name }} ({{ '@'.$user->username }})</p>
    </div>
    <div class="body">
        @if(session('success'))
            <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700;">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="background:#FEF3F2; border:1px solid #FECDCA; border-radius:12px; padding:12px 14px; color:#B42318; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.users.messages.store', $user) }}">
            @csrf

            <div class="form-section">
                <div class="field">
                    <label for="title">Judul Pesan</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Arahan Khusus Pendampingan" required maxlength="255">
                </div>

                <div class="field">
                    <label for="content">Isi Pesan Khusus</label>
                    <textarea id="content" name="content" rows="6" placeholder="Tuliskan pesan khusus untuk bruder bersangkutan di sini..." required>{{ old('content') }}</textarea>
                </div>

                <p style="margin:0; font-size:12px; color:#667085; line-height:1.45;">
                    Pesan ini dikirimkan secara privat dan langsung tampil pada Dashboard aplikasi bruder yang bersangkutan.
                </p>
            </div>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn-submit-gold">KIRIM PESAN SEKARANG</button>
                <a href="{{ route('superadmin.users.index') }}" class="back-link">Batal</a>
            </div>
        </form>

        @if($sentMessages->isNotEmpty())
            <div style="margin-top:20px;">
                <div style="font-size:13px; font-weight:800; color:#475467; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Pesan yang Pernah Dikirim</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach($sentMessages as $msg)
                        <div class="msg-history-card">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong style="color:#101828;">{{ $msg->title }}</strong>
                                <span style="font-size:11px; color:#667085;">{{ $msg->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <div style="color:#475467; line-height:1.4;">{{ \Illuminate\Support\Str::limit($msg->content, 120) }}</div>
                            <div style="font-size:11px; margin-top:4px; font-weight:700; color: {{ $msg->read_at ? '#027A48' : '#B57900' }};">
                                {{ $msg->read_at ? 'Dibaca pada ' . $msg->read_at->translatedFormat('d M Y H:i') : 'Belum dibaca' }}
                            </div>
                            <form method="POST" action="{{ route('superadmin.users.messages.destroy', [$user, $msg]) }}" onsubmit="return confirm('Tarik pesan ini? Bruder tidak akan melihatnya lagi.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-retract">Tarik Pesan</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @include('layouts.partials.sa-menu')
</div>
</body>
</html>
