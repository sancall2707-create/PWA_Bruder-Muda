<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Manajemen Data Bruder - PWA BRUDER FIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/fic-pwa.css">
    <style>
        body { background:#EEF2F6; font-family:'Plus Jakarta Sans','Inter',sans-serif; margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; }
        .container { width:100%; max-width:480px; background:#F6F8FC; min-height:100vh; display:flex; flex-direction:column; box-shadow:0 0 30px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg,#003E97 0%,#1455C0 100%); color:#fff; padding:20px; border-bottom-left-radius:20px; border-bottom-right-radius:20px; box-shadow:0 4px 16px rgba(0,62,151,0.15); }
        .header-nav { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .btn-header-back { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.3); border-radius:10px; color:#fff; text-decoration:none; font-size:16px; transition:transform 0.15s ease; }
        .btn-header-back:active { transform:scale(0.95); }
        .badge { display:inline-flex; gap:6px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; text-transform:uppercase; }
        .title { font-size:20px; font-weight:800; margin:0 0 4px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 40px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .search-form { display:flex; gap:8px; }
        .search-form input { flex:1; padding:12px 14px; border-radius:12px; border:1px solid #E4E7EC; background:#fff; font-size:13px; }
        .search-form button { background:#1455C0; color:#fff; border:none; padding:12px 20px; border-radius:12px; font-weight:700; cursor:pointer; }
        .list { display:flex; flex-direction:column; gap:10px; }
        .card { background:#fff; border:1px solid #E4E7EC; border-radius:14px; padding:14px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 6px rgba(16,24,40,0.03); }
        .name { font-weight:700; color:#101828; }
        .loc { font-size:12px; color:#667085; margin-top:2px; }
        .btn { background:#1455C0; color:#fff; padding:10px 16px; border-radius:10px; font-weight:700; text-decoration:none; font-size:13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-nav">
                <a href="{{ route('superadmin.fase2-check') }}" class="btn-header-back" aria-label="Kembali ke Dashboard">
                    ←
                </a>
                <div class="badge"><span>👥</span> MANAJEMEN BRUDER</div>
            </div>
            <h1 class="title">Daftar Bruder Muda</h1>
            <p class="subtitle">Kelola akun, cari cepat, tambah baru.</p>
        </div>
        <div class="body">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari nama, username, komunitas..." value="{{ request('search') }}">
                <button type="submit">Cari</button>
            </form>

            <div class="list">
                @forelse($users as $u)
                    <div class="card">
                        <div>
                            <div class="name">{{ $u->name }}</div>
                            <div class="loc">{{ $u->community_location ?? 'Komunitas belum diset' }}</div>
                        </div>
                        <a href="{{ route('superadmin.users.track-record', $u->id) }}" class="btn">Track Record</a>
                    </div>
                @empty
                    <div class="card" style="text-align:center; color:#667085;">Belum ada data bruder muda.</div>
                @endforelse
            </div>

            {{ $users->links() }}

            <a href="{{ route('superadmin.users.create') }}" class="btn" style="width:100%; text-align:center; box-sizing:border-box; padding:14px; margin-top:8px;">+ Tambah Bruder Baru</a>
        </div>
    </div>
</body>
</html>
