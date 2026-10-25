<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Manajemen Data Bruder - PWA BRUDER FIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/fic-pwa.css">
    <style>
        body { background:#e7f0fb; background-image:linear-gradient(180deg,#d5e4f8 0%,#eef3fb 46%,#f7f9fc 100%); font-family:'Plus Jakarta Sans','Inter',sans-serif; margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; }
        .container { width:100%; max-width:480px; background:linear-gradient(180deg,rgba(20,85,192,0.1) 0%,rgba(234,243,255,0.55) 180px,rgba(247,249,252,0.88) 100%); min-height:100vh; display:flex; flex-direction:column; box-shadow:0 0 30px rgba(0,62,151,0.08); padding-bottom:72px; box-sizing:border-box; }
        .header { background:linear-gradient(135deg,rgba(0,62,151,0.9) 0%,rgba(20,85,192,0.78) 100%); color:#fff; padding:20px; border-bottom:1px solid rgba(255,255,255,0.28); border-bottom-left-radius:20px; border-bottom-right-radius:20px; box-shadow:0 8px 20px rgba(0,62,151,0.12); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); }
        .header-nav { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .btn-header-back { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; padding:0; line-height:0; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.3); border-radius:10px; color:#fff; text-decoration:none; transition:transform 0.15s ease; }
        .btn-header-back svg { display:block; }
        .btn-header-back:active { transform:scale(0.95); }
        .badge { display:inline-flex; gap:6px; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; text-transform:uppercase; }
        .title { font-size:20px; font-weight:800; margin:0 0 4px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 40px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .search-form { display:flex; gap:8px; }
        .search-form input { flex:1; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.84); background:rgba(255,255,255,0.72); backdrop-filter:blur(12px); font-size:13px; }
        .search-form button { background:#1455C0; color:#fff; border:none; padding:12px 20px; border-radius:12px; font-weight:700; cursor:pointer; }
        .list { display:flex; flex-direction:column; gap:10px; }
        .card { background:rgba(255,255,255,0.66); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border:1px solid rgba(255,255,255,0.84); border-radius:14px; padding:14px; display:flex; gap:12px; align-items:flex-start; box-shadow:0 8px 24px rgba(0,62,151,0.08); }
        .avatar { width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#1455C0,#003E97); color:#fff; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .name { font-weight:700; color:#101828; }
        .loc { font-size:12px; color:#667085; margin-top:2px; }
        .meta { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
        .pill { font-size:11px; font-weight:800; border-radius:999px; padding:3px 8px; }
        .actions { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin-top:10px; }
        .actions form { margin:0; display:flex; }
        .btn { background:#1455C0; color:#fff; min-height:36px; padding:8px 12px; border-radius:10px; font-weight:700; text-decoration:none; font-size:12px; line-height:1; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; text-align:center; white-space:nowrap; }
        .btn-success { background:#027A48; color:#fff; }
        .btn-msg { background:#B57900; color:#fff; }
        .btn { background:#1455C0; color:#fff; min-height:36px; padding:8px 12px; border-radius:10px; font-weight:700; text-decoration:none; font-size:12px; line-height:1; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; text-align:center; white-space:nowrap; }
        .btn-muted { background:#F2F4F7; color:#344054; }
        .btn-danger { background:#B42318; color:#fff; }
        .fab { position:fixed; right:max(16px, calc(50vw - 224px)); bottom:80px; z-index:90; width:56px; height:56px; border-radius:18px; background:#1455C0; color:#fff; display:flex; align-items:center; justify-content:center; text-decoration:none; line-height:0; padding:0; box-shadow:0 8px 18px rgba(20,85,192,0.35); }
        .fab svg { display:block; }
        .body { padding-bottom:120px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-nav">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('superadmin.dashboard') }}" class="btn-header-back" aria-label="Kembali ke Beranda">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </a>
                <div class="badge">{{ auth()->user()->role === 'admin' ? 'DIREKTORI BRUDER' : 'MANAJEMEN BRUDER' }}</div>
                @include('layouts.partials.sa-menu-toggle')
            </div>
            <h1 class="title">{{ auth()->user()->role === 'admin' ? 'Direktori Bruder Muda' : 'Manajemen Data Bruder' }}</h1>
            <p class="subtitle">{{ auth()->user()->role === 'admin' ? 'Pantau data diri dan capaian pembinaan bruder.' : 'Kelola akun, cari cepat, tambah baru.' }}</p>
        </div>
        <div class="body">
            @if(session('success'))
                <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" class="search-form" id="searchUserForm" action="{{ route('superadmin.users.index') }}">
                <input type="text" id="searchUserInput" name="search" placeholder="Cari nama, username, komunitas..." value="{{ $search }}" autocomplete="off">
                <button type="submit">Cari</button>
            </form>

            <div id="usersListWrapper">
                @include('super-admin.users._list')
            </div>
        </div>
        @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('superadmin.users.create') }}" class="fab" aria-label="Tambah bruder muda">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </a>
        @endif
        @include('layouts.partials.sa-bottom-nav')
        @include('layouts.partials.sa-menu')
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchUserInput');
        const listWrapper = document.getElementById('usersListWrapper');
        if (!searchInput || !listWrapper) return;

        let debounceTimer = null;
        let abortController = null;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                const url = new URL('{{ route('superadmin.users.index') }}', window.location.origin);
                if (query) {
                    url.searchParams.set('search', query);
                }

                // Efek visual saat mencari
                listWrapper.style.opacity = '0.55';
                listWrapper.style.pointerEvents = 'none';

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    signal: abortController.signal
                })
                .then(res => res.text())
                .then(html => {
                    listWrapper.innerHTML = html;
                    listWrapper.style.opacity = '1';
                    listWrapper.style.pointerEvents = 'auto';
                    window.history.replaceState({}, '', url.toString());
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        listWrapper.style.opacity = '1';
                        listWrapper.style.pointerEvents = 'auto';
                    }
                });
            }, 250);
        });
    });
    </script>
</body>
</html>
