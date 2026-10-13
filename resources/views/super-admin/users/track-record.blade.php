<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Track Record {{ $user->name }} - PWA BRUDER FIC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/fic-pwa.css">
    <style>
        body { background:#EEF2F6; font-family:'Plus Jakarta Sans','Inter',sans-serif; margin:0; padding:0; min-height:100vh; display:flex; justify-content:center; }
        .container { width:100%; max-width:480px; background:#F6F8FC; min-height:100vh; display:flex; flex-direction:column; box-shadow:0 0 30px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg,#003E97 0%,#1455C0 100%); color:#fff; padding:20px; border-bottom-left-radius:20px; border-bottom-right-radius:20px; box-shadow:0 4px 16px rgba(0,62,151,0.15); }
        .header-nav { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
        .btn-header-back { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; background:rgba(255,255,255,0.16); border:1px solid rgba(255,255,255,0.3); border-radius:10px; color:#fff; text-decoration:none; font-size:16px; }
        .title { font-size:20px; font-weight:800; margin:0 0 2px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 40px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .card { background:#fff; border-radius:14px; border:1px solid #E4E7EC; padding:14px; display:flex; flex-direction:column; gap:10px; box-shadow:0 2px 6px rgba(16,24,40,0.03); }
        .card-title { font-size:12px; font-weight:800; color:#1455C0; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin-bottom:4px; }
        .detail-row { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; font-size:13px; }
        .detail-label { font-weight:700; color:#667085; text-transform:uppercase; letter-spacing:0.4px; font-size:11px; }
        .detail-value { font-weight:700; color:#101828; word-break:break-word; }
        .back-link { display:block; text-align:center; margin-top:8px; color:#1455C0; text-decoration:none; font-weight:700; font-size:13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-nav">
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali">←</a>
            <h1 class="title">Track Record Individual</h1>
        </div>
        <p class="subtitle">{{ $user->name }} ({{ '@' . $user->username }})</p>
    </div>
    <div class="body">
        <div class="card">
            <div class="card-title">Identitas & Data Diri</div>
            <div class="detail-row"><span class="detail-label">Tanggal Lahir</span><span class="detail-value">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->translatedFormat('d F Y') : '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Nama Orang Tua</span><span class="detail-value">{{ $user->parents_name ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Pendidikan Terakhir</span><span class="detail-value">{{ $user->last_education ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Tahun Masuk FIC</span><span class="detail-value">{{ $user->entry_year ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Komunitas</span><span class="detail-value">{{ $user->community_location ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">No. Telepon</span><span class="detail-value">{{ $user->phone ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Bio</span><span class="detail-value">{{ $user->bio ?? '-' }}</span></div>
        </div>

        <div class="card">
            <div class="card-title">Statistik Pembinaan</div>
            <div class="detail-row"><span class="detail-label">Bimbingan Rohani</span><span class="detail-value">{{ $user->spiritualGuidanceReports->count() }} Laporan</span></div>
            <div class="detail-row"><span class="detail-label">Wawanhati Pilok</span><span class="detail-value">{{ $user->wawanhatiReports->count() }} Laporan</span></div>
            <div class="detail-row"><span class="detail-label">Jurnal Pengembangan Diri</span><span class="detail-value">{{ $user->selfDevelopmentJournals->count() }} Jurnal</span></div>
            <div class="detail-row"><span class="detail-label">Agenda Pribadi</span><span class="detail-value">{{ $user->personalAgendas->count() }} Item</span></div>
        </div>

        <a href="{{ route('superadmin.users.index') }}" class="back-link">← Kembali ke Daftar Bruder</a>
    </div>
</div>
</body>
</html>