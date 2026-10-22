<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    @include('partials.fic-icons')
    <title>Track Record {{ $user->name }} - PWA BRUDER FIC</title>
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
        .title { font-size:20px; font-weight:800; margin:0 0 2px; }
        .subtitle { font-size:13px; color:rgba(255,255,255,.85); margin:0; }
        .body { padding:20px 16px 96px; flex:1; display:flex; flex-direction:column; gap:16px; }
        .card { background:rgba(255,255,255,0.66); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-radius:14px; border:1px solid rgba(255,255,255,0.84); padding:14px; display:flex; flex-direction:column; gap:10px; box-shadow:0 8px 24px rgba(0,62,151,0.08); }
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
            <a href="{{ route('superadmin.users.index') }}" class="btn-header-back" aria-label="Kembali"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
            <h1 class="title">Track Record Individual</h1>
            @include('layouts.partials.sa-menu-toggle')
        </div>
        <p class="subtitle">{{ $user->name }} ({{ '@' . $user->username }})</p>
    </div>
    <div class="body">
        @if(session('success'))
            <div style="background:#ECFDF3; border:1px solid #ABEFC6; color:#027A48; padding:12px 14px; border-radius:12px; font-size:13px; font-weight:700;">
                {{ session('success') }}
            </div>
        @endif
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
            <div class="detail-row"><span class="detail-label">Status akun</span><span class="detail-value">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
            <div class="detail-row"><span class="detail-label">Bimbingan Rohani</span><span class="detail-value">{{ $user->spiritual_guidance_reports_count }} Laporan</span></div>
            <div class="detail-row"><span class="detail-label">Wawanhati Pilok</span><span class="detail-value">{{ $user->wawanhati_reports_count }} Laporan</span></div>
            <div class="detail-row"><span class="detail-label">Jurnal Pengembangan Diri</span><span class="detail-value">{{ $user->self_development_journals_count }} Jurnal</span></div>
            <div class="detail-row"><span class="detail-label">Agenda Pribadi</span><span class="detail-value">{{ $user->personal_agendas_count }} Item</span></div>
        </div>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin-bottom:6px;">
                <div class="card-title" style="border:none; padding:0; margin:0;">Catatan Pembinaan (TPPBM)</div>
                <a href="{{ route('superadmin.users.notes.create', $user) }}" style="font-size:11px; font-weight:800; color:#027A48; text-decoration:none; background:#ECFDF3; border:1px solid #ABEFC6; padding:4px 8px; border-radius:8px;">+ Tambah Catatan</a>
            </div>
            @forelse($notes as $note)
                <div style="padding:10px; background:#F8FAF9; border-left:3px solid #027A48; border-radius:0 8px 8px 0; margin-bottom:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <span style="font-size:11px; font-weight:800; color:#027A48; text-transform:uppercase;">{{ $note->category }}</span>
                        <span style="font-size:11px; color:#667085;">{{ $note->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    <div style="font-size:13px; color:#344054; line-height:1.45; white-space:pre-line;">{{ $note->content }}</div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px; font-size:11px; color:#667085;">
                        <div>Oleh: {{ $note->author->name ?? 'Admin TPPBM' }}</div>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('superadmin.users.notes.edit', [$user, $note]) }}" style="background:#FFF9EB; border:1px solid #FCD34D; color:#B45309; padding:2px 8px; border-radius:6px; font-weight:700; text-decoration:none; font-size:11px;">Edit</a>
                            <form method="POST" action="{{ route('superadmin.users.notes.destroy', [$user, $note]) }}" onsubmit="return confirm('Tarik / hapus catatan ini?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:#FEF3F2; border:1px solid #FECDCA; color:#B42318; padding:2px 8px; border-radius:6px; font-weight:700; cursor:pointer; font-size:11px; font-family:inherit;">Tarik</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="detail-value" style="color:#667085;">Belum ada catatan pembinaan khusus.</div>
            @endforelse
        </div>

        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #F2F4F7; padding-bottom:8px; margin-bottom:6px;">
                <div class="card-title" style="border:none; padding:0; margin:0;">Pesan Khusus Terkirim</div>
                <a href="{{ route('superadmin.users.messages.create', $user) }}" style="font-size:11px; font-weight:800; color:#B57900; text-decoration:none; background:#FFF9EB; border:1px solid #FECDCA; padding:4px 8px; border-radius:8px;">+ Kirim Pesan</a>
            </div>
            @forelse($messages as $msg)
                <div style="padding:10px; background:#FDFCF8; border-left:3px solid #B57900; border-radius:0 8px 8px 0; margin-bottom:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <strong style="font-size:13px; color:#101828;">{{ $msg->title }}</strong>
                        <span style="font-size:11px; font-weight:700; color: {{ $msg->read_at ? '#027A48' : '#B57900' }};">{{ $msg->read_at ? 'Sudah dibaca' : 'Belum dibaca' }}</span>
                    </div>
                    <div style="font-size:12px; color:#475467; line-height:1.4;">{{ \Illuminate\Support\Str::limit($msg->content, 120) }}</div>
                    <div style="font-size:11px; color:#667085; margin-top:4px;">Dikirim: {{ $msg->created_at->translatedFormat('d M Y, H:i') }} oleh {{ $msg->sender->name ?? 'Admin' }}</div>
                    @if(auth()->user()->role === 'super_admin' || $msg->sender_id === auth()->id())
                        <form method="POST" action="{{ route('superadmin.users.messages.destroy', [$user, $msg]) }}" onsubmit="return confirm('Tarik pesan ini? Bruder tidak akan melihatnya lagi.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="margin-top:8px; border:1px solid #FECDCA; background:#FEF3F2; color:#B42318; border-radius:8px; padding:6px 10px; font-size:11px; font-weight:800; cursor:pointer; font-family:inherit;">Tarik Pesan</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="detail-value" style="color:#667085;">Belum ada pesan khusus terkirim.</div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-title">Laporan Terbaru</div>
            @forelse($recentBimbingan as $report)
                <div class="detail-row"><span class="detail-label">Bimbingan</span><span class="detail-value">{{ $report->mentor_name }}</span></div>
            @empty
            @endforelse
            @forelse($recentWawanhati as $report)
                <div class="detail-row"><span class="detail-label">Wawanhati</span><span class="detail-value">{{ $report->local_leader_name }}</span></div>
            @empty
            @endforelse
            @forelse($recentJournals as $journal)
                <div class="detail-row"><span class="detail-label">Jurnal</span><span class="detail-value">{{ $journal->activity_name }}</span></div>
            @empty
            @endforelse
            @if($recentBimbingan->isEmpty() && $recentWawanhati->isEmpty() && $recentJournals->isEmpty())
                <div class="detail-value">Belum ada laporan tersimpan.</div>
            @endif
        </div>

        <a href="{{ route('superadmin.users.index') }}" class="back-link">← Kembali ke Daftar Bruder</a>
    </div>
    @include('layouts.partials.sa-menu')
</div>
</body>
</html>